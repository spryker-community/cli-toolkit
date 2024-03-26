<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerCommunity\CliToolKit\Translator;

class TranslationRequest
{
    protected string $importType;

    protected string $filePath;

    protected int $line;

    protected string $key;

    protected string $text;

    /**
     * @param string $importType
     * @param string $filePath
     * @param int $line
     * @param string $key
     * @param string $text
     */
    public function __construct(string $importType, string $filePath, int $line, string $key, string $text)
    {
        $this->importType = $importType;
        $this->filePath = $filePath;
        $this->line = $line;
        $this->key = $key;
        $this->text = $text;
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
     * @return int
     */
    public function getLine(): int
    {
        return $this->line;
    }
}
