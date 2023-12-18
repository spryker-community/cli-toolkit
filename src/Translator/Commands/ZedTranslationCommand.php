<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerCommunity\Toolkit\Translator\Commands;

use Monolog\Logger;
use SprykerCommunity\Toolkit\Translator\Exception\TranslatorException;
use SprykerCommunity\Toolkit\Translator\ZedTranslator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ZedTranslationCommand extends AbstractTranslationCommand
{
    /**
     * @return void
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('translation:zed:generate')
            ->setDescription('Generates Zed translations to the specified target locale');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);

        try {
            $translatorService = new ZedTranslator($this->translationEngine, $this->container->get(Logger::class));
            $translatorService->setOutput($output);
            $translatorService->translate($this->directory, $this->locales);
        } catch (TranslatorException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * @param string|null $directory
     *
     * @return string
     */
    protected function getDirectory(?string $directory): string
    {
        if (!$directory) {
            $directory = dirname(__DIR__, 3);
        }

        return $directory;
    }
}
