<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerCommunity\CliToolKit\Translator;

class CategoryTranslator extends AbstractYvesTranslator
{
    /**
     * @return string
     */
    public function getType(): string
    {
        return 'category';
    }

    /**
     * @return string
     */
    protected function getKeyBlacklistPattern(): string
    {
        return '';
    }
}
