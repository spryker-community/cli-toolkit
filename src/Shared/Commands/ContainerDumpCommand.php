<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerCommunity\Toolkit\Shared\Commands;

use SprykerCommunity\Toolkit\Shared\Container\ContainerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Dumper\XmlDumper;

class ContainerDumpCommand extends Command
{
    use ContainerAwareTrait;

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('container:dump');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dumper = new XmlDumper($this->container);

        file_put_contents(__DIR__ . '/../../../var/cache/container.xml', $dumper->dump());

        return static::SUCCESS;
    }
}
