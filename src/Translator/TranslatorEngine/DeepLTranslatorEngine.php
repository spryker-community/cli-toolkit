<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerCommunity\CliToolKit\Translator\TranslatorEngine;

use DeepL\DeepLException;
use DeepL\Translator;
use SprykerCommunity\CliToolKit\Translator\Exception\TranslatorException;

class DeepLTranslatorEngine implements TranslatorEngineInterface
{
    /**
     * @var string
     */
    protected const LOCALE_EN = 'en';

    /**
     * @var string
     */
    public const CLI_TOOLKIT_DEEPL_API_AUTH_KEY = 'CLI_TOOLKIT_DEEPL_API_AUTH_KEY';

    protected Translator $translator;

    /**
     * @param string $apiKey
     */
    public function __construct(string $apiKey)
    {
        $this->translator = new Translator($apiKey);
    }

    /**
     * @param string $text
     * @param string $targetLang
     * @param string $sourceLang
     *
     * @throws \SprykerCommunity\CliToolKit\Translator\Exception\TranslatorException
     *
     * @return string
     */
    public function translate(string $text, string $targetLang, string $sourceLang): string
    {
        $sourceLang = $this->getSourceLang($sourceLang);
        $targetLang = $this->getTargetLang($targetLang);

        try {
            $translation = $this->translator->translateText($text, $sourceLang, $targetLang);

            if (is_array($translation)) {
                throw new TranslatorException('Unexpected DeepL array result');
            }
        } catch (DeepLException $e) {
            throw new TranslatorException($e->getMessage());
        }

        return $translation->text;
    }

    /**
     * @param string $targetLang
     *
     * @return string
     */
    protected function getTargetLang(string $targetLang): string
    {
        $lang = substr($targetLang, 0, 2);
        if ($lang === static::LOCALE_EN) {
            $targetLang = str_replace('_', '-', $targetLang);
        } else {
            $targetLang = strtoupper($lang);
        }

        return $targetLang;
    }

    /**
     * @param string $sourceLang
     *
     * @return string
     */
    protected function getSourceLang(string $sourceLang): string
    {
        $lang = substr($sourceLang, 0, 2);
        $sourceLang = strtoupper($lang);

        return $sourceLang;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'DeepL';
    }
}
