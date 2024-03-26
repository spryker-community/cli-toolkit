<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerCommunity\CliToolKit\Translator\Config;

class ZedTranslatorConfig
{
    /**
     * @api
     *
     * @return array<string>
     */
    public function getTranslationFilePathPatterns(): array
    {
        return [
            '/src/Pyz/Zed/Translator/data/*/',
            '/vendor/spryker/*/data/translation/Zed/',
            '/vendor/spryker/spryker-demo/Bundles/*/data/translation/Zed/',
            '/vendor/spryker/spryker/Bundles/*/data/translation/Zed/',
        ];
    }

    /**
     * @api
     *
     * @return string
     */
    public function getProjectLevelPath(): string
    {
        return 'src/Pyz/Zed/Translator/data/';
    }
}
