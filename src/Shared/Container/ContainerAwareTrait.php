<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerCommunity\Toolkit\Shared\Container;

use Symfony\Component\DependencyInjection\ContainerBuilder;

trait ContainerAwareTrait
{
    protected ContainerBuilder $container;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function setContainer(ContainerBuilder $container): void
    {
        $this->container = $container;
    }
}
