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

class GlossaryTranslator extends AbstractYvesTranslator
{
    /**
     * @param string $directory
     * @param iterable<\SprykerCommunity\CliToolKit\Translator\TranslationResponse> $records
     *
     * @return void
     */
    public function addTranslatedRecords(string $directory, iterable $records): void
    {
        $recordsByFile = [];
        foreach ($records as $record) {
            $recordsByFile[$record->getFilePath()][] = $record;
        }

        foreach ($recordsByFile as $filePath => $translations) {
            $writer = Writer::createFromPath($filePath, 'a');
            foreach ($translations as $translation) {
                $writer->insertOne([
                    'key' => $translation->getKey(),
                    'translation' => $translation->getText(),
                    'locale' => $translation->getLocale(),
                ]);
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
        try {
            $csv = Reader::createFromPath($filePath, 'r');
            $recordsToBeTranslated = [];

            if ($csv->count()) {
                $csv->setHeaderOffset(0); // Set the CSV header offset
                $stmt = Statement::create();

                foreach ($stmt->process($csv) as $rowOffset => $record) {
                    if (!isset($record['key']) || !isset($record['locale']) || !isset($record['translation'])) {
                        continue;
                    }

                    $key = $record['key'];
                    $recordLocale = $record['locale'];
                    $translation = $record['translation'];

                    if ($recordLocale === static::SOURCE_LOCALE && !isset($recordsToBeTranslated[$key])) {
                        $recordsToBeTranslated[$key] = new TranslationRequest(
                            $this->getType(),
                            $filePath,
                            $rowOffset,
                            $key,
                            $translation,
                        );
                    } elseif ($recordLocale === $locale && isset($recordsToBeTranslated[$key])) {
                        unset($recordsToBeTranslated[$key]);
                    }
                }
            }
        } catch (Exception $exception) {
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
    public function getType(): string
    {
        return 'glossary';
    }
}
