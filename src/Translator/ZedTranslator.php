<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerCommunity\Toolkit\Translator;

use Exception;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\SyntaxError;
use League\Csv\Writer;
use Psr\Log\LoggerInterface;
use SprykerCommunity\Toolkit\Shared\Output\OutputAwareTrait;
use SprykerCommunity\Toolkit\Translator\Config\ZedTranslatorConfig;
use SprykerCommunity\Toolkit\Translator\Finder\TranslationFinderInterface;
use SprykerCommunity\Toolkit\Translator\Finder\ZedTranslationFinder;
use SprykerCommunity\Toolkit\Translator\TranslatorEngine\TranslatorEngineInterface;
use Throwable;

class ZedTranslator
{
    use OutputAwareTrait;

    /**
     * @var int
     */
    protected const MAX_RETRIES = 360;

    /**
     * @var int
     */
    protected const RETRIES_WAITING_SECONDS = 30;

    /**
     * @var string
     */
    protected const SOURCE_LOCALE = 'en_US';

    /**
     * @var \SprykerCommunity\Toolkit\Translator\Config\ZedTranslatorConfig
     */
    protected ZedTranslatorConfig $translatorConfig;

    /**
     * @var \SprykerCommunity\Toolkit\Translator\Finder\TranslationFinderInterface
     */
    protected TranslationFinderInterface $translationFinder;

    private TranslatorEngineInterface $translator;

    private LoggerInterface $logger;

    /**
     * @param \SprykerCommunity\Toolkit\Translator\TranslatorEngine\TranslatorEngineInterface $translator
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(TranslatorEngineInterface $translator, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->translatorConfig = new ZedTranslatorConfig();
        $this->translationFinder = new ZedTranslationFinder();
        $this->translator = $translator;
    }

    /**
     * @param string $baseDirectory
     * @param array<int<0, max>, string> $locales
     *
     * @return void
     */
    public function translate(string $baseDirectory, array $locales): void
    {
        $this->printInfoMessage();

        $directories = $this->prepareTranslationFilePaths(
            $this->translatorConfig->getTranslationFilePathPatterns(),
            $baseDirectory,
        );
        $moduleDirectories = $this->translationFinder->findFilesByGlobPatterns($directories);
        asort($moduleDirectories);

        foreach ($moduleDirectories as $moduleDirectory) {
            foreach ($locales as $targetLocale) {
                $projectDirectory = $baseDirectory . '/' . $this->getDirectoryToWrite($moduleDirectory, $targetLocale);
                $moduleName = $this->getModuleName($moduleDirectory);
                $this->output->writeln(PHP_EOL . "Checking if there are glossary translations to be generated for module $moduleName to the $targetLocale locale:");
                $records = $this->getRecordsToBeTranslated(
                    $baseDirectory,
                    $targetLocale,
                    $moduleDirectory,
                );
                if (!$records) {
                    $this->output->writeln('No glossaries found that require translations for the locale ' . $targetLocale);

                    continue;
                }

                $this->output->writeln("Generating glossary translations for module $moduleName in the $targetLocale locale...");
                $this->translateRecords($records, $targetLocale, $projectDirectory);
                foreach ($records as $record) {
                    $this->output->writeln($record->getKey());
                }
            }
        }
    }

    /**
     * @param array<string|int, \SprykerCommunity\Toolkit\Translator\TranslationRequest> $records
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
            do {
                try {
                    $translation = $this->translator->translate(
                        $record->getText(),
                        $targetLocale,
                        static::SOURCE_LOCALE,
                    );

                    $translatedRecord = [
                        $record->getKey(),
                        $translation,
                    ];

                    if (method_exists($this, 'createDirectoryIfNotExists')) {
                        $this->createDirectoryIfNotExists($directory);
                    }

                    break;
                } catch (Throwable $e) {
                    $this->logger->error($e->getMessage());
                    $retry++;
                    sleep(static::RETRIES_WAITING_SECONDS);

                    if ($retry < static::MAX_RETRIES) {
                        $this->output->writeln(sprintf(
                            '<error>Translation unsuccessful. Retrying in %s seconds.</error>',
                            static::RETRIES_WAITING_SECONDS,
                        ));

                        continue;
                    }

                    throw $e;
                }
            } while ($retry < static::MAX_RETRIES);
            $this->addTranslatedRecords($directory, [$translatedRecord]);
        }
    }

    /**
     * @param string $directory
     * @param array<int, array<int, string>> $records
     *
     * @return void
     */
    public function addTranslatedRecords(string $directory, array $records): void
    {
        $writer = Writer::createFromPath($directory, 'a');
        $writer->insertAll($records);
    }

    /**
     * @param string $directory
     * @param array<string, string> $recordsToBeTranslated
     *
     * @throws \League\Csv\SyntaxError
     *
     * @return array<string, string>
     */
    protected function getZedRecordsForTranslation(
        string $directory,
        array $recordsToBeTranslated
    ): array {
        try {
            $csv = Reader::createFromPath($directory, 'r');

            if ($csv->count()) {
                $stmt = Statement::create();
                foreach ($stmt->process($csv) as $record) {
                    $record = array_values($record);
                    if (!$record[0] || !$record[1]) {
                        continue;
                    }

                    $key = (string)$record[0];
                    $translation = (string)$record[1];

                    if (!isset($recordsToBeTranslated[$key])) {
                        $recordsToBeTranslated[$key] = $translation;
                    }
                }
            }
        } catch (SyntaxError $exception) {
            throw new SyntaxError($exception->getMessage());
        }

        return $recordsToBeTranslated;
    }

    /**
     * @param string $file
     * @param string $targetLocale
     *
     * @return string
     */
    protected function getDirectoryToWrite(string $file, string $targetLocale): string
    {
        $moduleName = $this->getModuleName($file);

        return $this->translatorConfig->getProjectLevelPath() . $moduleName . '/' . $targetLocale . '.csv';
    }

    /**
     * @param string $moduleDirectory
     *
     * @return string
     */
    protected function getModuleName(string $moduleDirectory): string
    {
        $chunks = explode('/', $moduleDirectory);
        if (in_array('Translator', $chunks)) {
            $moduleName = $chunks[count($chunks) - 2];
        } else {
            $moduleName = $chunks[count($chunks) - 5];
        }

        return str_replace('-', '', ucwords($moduleName, '-'));
    }

    /**
     * @param string $directory
     *
     * @return void
     */
    protected function createDirectoryIfNotExists(string $directory): void
    {
        if (!file_exists($directory)) {
            if (!file_exists(dirname($directory, 1))) {
                mkdir(dirname($directory, 1), 0777, true);
            }
            $stream = fopen($directory, 'w'); // Create the file for writing.
            if ($stream) {
                fclose($stream);
            }
        }
    }

    /**
     * @param array<string> $directories
     * @param string $baseDirectory
     *
     * @return array<string>
     */
    protected function prepareTranslationFilePaths(array $directories, string $baseDirectory): array
    {
        $fullDirectories = [];
        foreach ($directories as $directory) {
            $fullDirectories[] = $baseDirectory . $directory;
        }

        return $fullDirectories;
    }

    /**
     * @param string $baseDirectory
     * @param string $targetLocale
     * @param string $moduleDirectory
     *
     * @return array<\SprykerCommunity\Toolkit\Translator\TranslationRequest>
     */
    protected function getRecordsToBeTranslated(
        string $baseDirectory,
        string $targetLocale,
        string $moduleDirectory
    ): array {
        $recordsToBeTranslated = [];
        $targetLocaleExistingRecords = [];
        $directory_locales = [];

        if (scandir($moduleDirectory)) {
            $directory_locales = array_slice(scandir($moduleDirectory), 2);
        }

        foreach ($directory_locales as $directory_locale) {
            $file_locale = trim($directory_locale, '.csv');
            if ($file_locale === static::SOURCE_LOCALE) {
                $recordsToBeTranslated = $this->getZedRecordsForTranslation(
                    $moduleDirectory . $directory_locale,
                    $recordsToBeTranslated,
                );
            } elseif ($targetLocale === $file_locale) {
                $targetLocaleExistingRecords = $this->getZedRecordsForTranslation(
                    $moduleDirectory . $directory_locale,
                    $targetLocaleExistingRecords,
                );
            }
        }
        $records = [];
        if (count($targetLocaleExistingRecords)) {
            foreach (array_keys($recordsToBeTranslated) as $record) {
                if (!array_key_exists($record, $targetLocaleExistingRecords)) {
                    $records[$record] = $recordsToBeTranslated[$record];
                }
            }
        } else {
            $records = $recordsToBeTranslated;
        }

        $moduleName = $this->getModuleName($moduleDirectory);
        $projectDirectory = $baseDirectory . '/' . $this->translatorConfig->getProjectLevelPath() . $moduleName . '/' . $targetLocale . '.csv';
        if (file_exists($projectDirectory)) {
            $records = $this->excludeProjectLevelKeys($projectDirectory, $records);
        }

        $finalRecords = [];
        foreach ($records as $key => $record) {
            $finalRecords[$key] = new TranslationRequest(
                '',
                '', #$filePath,
                0, #$rowOffset,
                $key,
                $record,
            );
        }

        return $finalRecords;
    }

    /**
     * @param string $projectDirectory
     * @param array<string, string> $records
     *
     * @throws \Exception
     *
     * @return array<string, string>
     */
    protected function excludeProjectLevelKeys(string $projectDirectory, array $records): array
    {
        try {
            $projectLevelTranslatedRecords = $this->getZedRecordsForTranslation(
                $projectDirectory,
                [],
            );
        } catch (SyntaxError $exception) {
            throw new Exception($exception->getMessage());
        }

        foreach (array_keys($projectLevelTranslatedRecords) as $projectRecord) {
            if (array_key_exists($projectRecord, $records)) {
                unset($records[$projectRecord]);
            }
        }

        return $records;
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
