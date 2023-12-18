<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerCommunity\Toolkit\Translator;

class TranslationResponse
{
    protected string $importType;

    protected string $filePath;

    protected int $line;

    protected string $key;

    protected string $text;

    protected string $locale;

    /**
     * @param string $importType
     * @param string $filePath
     * @param int $line
     * @param string $key
     * @param string $text
     * @param string $locale
     */
    public function __construct(string $importType, string $filePath, int $line, string $key, string $text, string $locale)
    {
        $this->filePath = $filePath;
        $this->importType = $importType;
        $this->line = $line;
        $this->key = $key;
        $this->text = $text;
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * @return string
     */
    public function getImportType(): string
    {
        return $this->importType;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @return int
     */
    public function getLine(): int
    {
        return $this->line;
    }
}
