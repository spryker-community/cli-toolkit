<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerCommunity\Toolkit\Translator\TranslatorEngine;

interface TranslatorEngineInterface
{
    /**
     * @param string $text
     * @param string $targetLang
     * @param string $sourceLang
     *
     * @return string
     */
    public function translate(string $text, string $targetLang, string $sourceLang): string;

    /**
     * @return string
     */
    public function getDescription(): string;
}
