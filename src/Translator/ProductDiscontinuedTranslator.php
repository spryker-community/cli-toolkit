<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerCommunity\Toolkit\Translator;

class ProductDiscontinuedTranslator extends AbstractYvesTranslator
{
    /**
     * @return string
     */
    protected function getType(): string
    {
        return 'product_discontinued';
    }

    /**
     * @return string
     */
    protected function getKeyBlacklistPattern(): string
    {
        return '';
    }
}
