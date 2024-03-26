<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerCommunity\CliToolKit\Translator;

use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\Writer;
use Psr\Log\LoggerInterface;
use SprykerCommunity\CliToolKit\Shared\Output\OutputAwareTrait;
use SprykerCommunity\CliToolKit\Translator\TranslatorEngine\TranslatorEngineInterface;
use Throwable;

abstract class AbstractYvesTranslator
{
    use OutputAwareTrait;

    /**
     * @var \SprykerCommunity\CliToolKit\Translator\TranslatorEngine\TranslatorEngineInterface
     */
    protected TranslatorEngineInterface $translator;

    /**
     * @var string
     */
    protected const SOURCE_LOCALE = 'en_US';

    /**
     * @var string
     */
    protected const DEFAULT_KEY = 'default';

    /**
     * @var int
     */
    protected const MAX_RETRIES = 360;

    /**
     * @var int
     */
    protected const RETRIES_WAITING_SECONDS = 30;

    private LoggerInterface $logger;

    /**
     * @param \SprykerCommunity\CliToolKit\Translator\TranslatorEngine\TranslatorEngineInterface $translator
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(TranslatorEngineInterface $translator, LoggerInterface $logger)
    {
        $this->translator = $translator;
        $this->logger = $logger;
    }

    /**
     * @param string $baseDirectory
     * @param array<string> $locales
     *
     * @return void
     */
    public function translate(string $baseDirectory, array $locales): void
    {
        $this->printInfoMessage();

        foreach ($locales as $targetLocale) {
            foreach ($this->getFilePaths($baseDirectory) as $file) {
                $this->output->writeln(sprintf(
                    'Checking if there are %s translations to be generated to the %s locale:',
                    $this->getType(),
                    $targetLocale,
                ));
                $records = $this->getYvesRecordsForTranslation($file, $targetLocale);

                if (!$records) {
                    $this->output->writeln('No items found that require translations for the locale ' . $targetLocale);

                    continue;
                }

                $this->output->writeln(sprintf(
                    'Generating %s translations to the %s locale...',
                    $this->getType(),
                    $targetLocale,
                ));
                $this->translateRecords($records, $targetLocale, $baseDirectory);
                foreach ($records as $key => $value) {
                    $this->output->writeln((string)$key);
                }
            }
        }
    }

    /**
     * @param string $filePath
     * @param string $locale
     *
     * @throws \League\Csv\Exception
     *
     * @return array<string|int, \SprykerCommunity\CliToolKit\Translator\TranslationRequest>
     */
    public function getYvesRecordsForTranslation(string $filePath, string $locale): array
    {
        $recordsToBeTranslated = [];

        // to optimize same text could be translated more than once in case it is repeated
        try {
            $csv = Reader::createFromPath($filePath, 'r');
            // validate if it has en_US columns
            $csv->setHeaderOffset(0); // Set the CSV header offset
            $matches = preg_grep('/.*\.' . static::SOURCE_LOCALE . '/', $csv->getHeader());

            if (!$matches) {
                return $recordsToBeTranslated;
            }

            $stmt = Statement::create();
            foreach ($stmt->process($csv) as $rowOffset => $row) {
                foreach ($row as $key => $value) {
                    // skip columns without header
                    if (!$key) {
                        continue;
                    }
                    if (!preg_match('/.*\.(' . static::SOURCE_LOCALE . ')|(' . static::DEFAULT_KEY . ')$/', $key) || !$value) {
                        continue;
                    }

                    // if column name includes key within the name skip as keys can not be translated
                    if ($this->getKeyBlacklistPattern() && preg_match('/' . $this->getKeyBlacklistPattern() . '/i', $key)) {
                        continue;
                    }

                    if (preg_match('/.*\.' . static::DEFAULT_KEY . '$/', $key)) {
                        $targetKey = str_replace(static::DEFAULT_KEY, $locale, $key);
                    } else {
                        $targetKey = str_replace(static::SOURCE_LOCALE, $locale, $key);
                    }

                    // skip if target field already has a value == not override
                    if (!empty($row[$targetKey])) {
                        continue;
                    }

                    $recordsToBeTranslated[] = new TranslationRequest(
                        $this->getType(),
                        $filePath,
                        $rowOffset,
                        $key,
                        $value,
                    );
                }
            }
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());

            throw new Exception($exception->getMessage());
        }

        return $recordsToBeTranslated;
    }

    /**
     * @param \SprykerCommunity\CliToolKit\Translator\TranslationRequest $value
     * @param string $translation
     * @param string $targetLocale
     *
     * @return \SprykerCommunity\CliToolKit\Translator\TranslationResponse
     */
    protected function prepareTranslatedRecords(TranslationRequest $value, string $translation, string $targetLocale): TranslationResponse
    {
        return new TranslationResponse(
            $value->getImportType(),
            $value->getFilePath(),
            $value->getLine(),
            $value->getKey(),
            $translation,
            $targetLocale,
        );
    }

    /**
     * @return string
     */
    abstract protected function getType(): string;

    /**
     * @return string
     */
    protected function getKeyBlacklistPattern(): string
    {
        return '';
    }

    /**
     * @param string $projectDirectory
     *
     * @return array<string>
     */
    protected function getFilePaths(string $projectDirectory): array
    {
        return array_merge(
            glob($projectDirectory . '/data/import/common/*/' . $this->getType() . '.csv') ?: [],
            glob($projectDirectory . '/data/import/common/*/*/' . $this->getType() . '.csv') ?: [],
        );
    }

    /**
     * @param string $directory
     * @param iterable<\SprykerCommunity\CliToolKit\Translator\TranslationResponse> $records
     *
     * @return void
     */
    public function addTranslatedRecords(string $directory, iterable $records): void
    {
        $newFileRecords = [];
        $translationsByFileAndLine = [];
        foreach ($records as $record) {
            $translationsByFileAndLine[$record->getFilePath()][$record->getLine()][] = $record;
            $translationsByFile[$record->getFilePath()][] = $record;
        }
        foreach ($translationsByFileAndLine as $filePath => $translation) {
            $originalCsvReader = Reader::createFromPath($filePath, 'r');
            $originalCsvReader->setHeaderOffset(0);
            $csvHeader = $originalCsvReader->getHeader();
            foreach ($translationsByFile[$filePath] as $modifiedValue) {
                if (preg_match('/.*\.' . static::DEFAULT_KEY . '$/', $modifiedValue->getKey())) {
                    $header = str_replace(static::DEFAULT_KEY, $modifiedValue->getLocale(), $modifiedValue->getKey());
                } else {
                    $header = str_replace(static::SOURCE_LOCALE, $modifiedValue->getLocale(), $modifiedValue->getKey());
                }
                if (in_array($header, $csvHeader)) {
                    continue;
                }

                $csvHeader[] = $header;
            }
            $newFileRecords[] = $csvHeader;

            foreach ($originalCsvReader->getRecords() as $line => $originalRecord) {
                if (!isset($translationsByFileAndLine[$filePath][$line])) {
                    $newFileRecords[] = $originalRecord;

                    continue;
                }

                foreach ($translationsByFileAndLine[$filePath][$line] as $modifiedValue) {
                    // no override if value is already existing
                    if (preg_match('/.*\.' . static::DEFAULT_KEY . '$/', $modifiedValue->getKey())) {
                        $originalRecord[str_replace(static::DEFAULT_KEY, $modifiedValue->getLocale(), $modifiedValue->getKey())] = $modifiedValue->getText();
                    } else {
                        $originalRecord[str_replace(static::SOURCE_LOCALE, $modifiedValue->getLocale(), $modifiedValue->getKey())] = $modifiedValue->getText();
                    }
                }
                $newFileRecords[] = $originalRecord;
            }

            $newCsvWriter = Writer::createFromPath($filePath . '.tmp', 'w');
            $newCsvWriter->insertAll($newFileRecords);
            unlink($filePath);
            rename($filePath . '.tmp', $filePath);
        }
    }

    /**
     * @param array<string|int, \SprykerCommunity\CliToolKit\Translator\TranslationRequest> $records
     * @param string $targetLocale
     * @param string $directory
     *
     * @throws \Throwable
     *
     * @return void
     */
    protected function translateRecords(array $records, string $targetLocale, string $directory): void
    {
        foreach ($records as $record) {
            $retry = 0;
            $translatedRecord = null;

            do {
                try {
                    $translation = $this->translator->translate(
                        $record->getText(),
                        $targetLocale,
                        static::SOURCE_LOCALE,
                    );

                    $translatedRecord = $this->prepareTranslatedRecords($record, $translation, $targetLocale);
                    if (method_exists($this, 'createDirectoryIfNotExists')) {
                        $this->createDirectoryIfNotExists($directory);
                    }

                    break;
                } catch (Throwable $e) {
                    $retry++;
                    sleep(static::RETRIES_WAITING_SECONDS);

                    if ($retry < static::MAX_RETRIES) {
                        $this->logger->error($e->getMessage(), [$translation ?? '']);
                        $this->output->writeln(sprintf(
                            '<error>Translation unsuccessful. Retrying in %s seconds.</error>',
                            static::RETRIES_WAITING_SECONDS,
                        ));

                        continue;
                    }

                    throw $e;
                }
            } while ($retry < static::MAX_RETRIES);

            if (!$translatedRecord) {
                continue;
            }

            $this->addTranslatedRecords($directory, [$translatedRecord]);
        }
    }

    /**
     * @return void
     */
    protected function printInfoMessage(): void
    {
        $message = 'Generating translations using ' . $this->translator->getDescription();
        $messageLength = strlen($message);

        $this->output->writeln([
            '<info>' . $message . '</>',
            '<info>' . str_repeat('=', $messageLength) . '</>',
            '',
        ]);
    }
}
