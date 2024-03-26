<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerCommunity\CliToolKit\Translator\Finder;

interface TranslationFinderInterface
{
    /**
     * c
     *
     * @param array<string> $translationFilePathPatterns
     *
     * @return array<string>
     */
    public function findFilesByGlobPatterns(array $translationFilePathPatterns): array;
}
