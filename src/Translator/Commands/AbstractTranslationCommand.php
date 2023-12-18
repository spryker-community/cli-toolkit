<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerCommunity\Toolkit\Translator\Commands;

use SprykerCommunity\Toolkit\Shared\Container\ContainerAwareTrait;
use SprykerCommunity\Toolkit\Translator\TranslatorEngine\ChatGptTranslatorEngine;
use SprykerCommunity\Toolkit\Translator\TranslatorEngine\DeepLTranslatorEngine;
use SprykerCommunity\Toolkit\Translator\TranslatorEngine\TranslatorEngineInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Intl\Locales;

abstract class AbstractTranslationCommand extends Command
{
    use ContainerAwareTrait;

    /**
     * @var string
     */
    protected const ARGUMENT_LOCALES = 'locales';

    /**
     * @var string
     */
    protected const WORKING_DIR = 'working-dir';

    /**
     * @var string
     */
    protected const TRANSLATION_ENGINE = 'translation-engine';

    /**
     * @var string
     */
    protected const CHATGPT_TRANSLATION_ENGINE = 'chatgpt';

    /**
     * @var string
     */
    protected const DEEPL_TRANSLATION_ENGINE = 'deepl';

    /**
     * @var string
     */
    protected string $directory;

    /**
     * @var array<string>
     */
    protected array $locales;

    /**
     * @var \SprykerCommunity\Toolkit\Translator\TranslatorEngine\TranslatorEngineInterface
     */
    protected TranslatorEngineInterface $translationEngine;

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->addArgument(static::ARGUMENT_LOCALES, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'The locales to which we want the Spryker glossary translated.')
            ->addOption(static::WORKING_DIR, 'd', InputOption::VALUE_OPTIONAL, 'If specified, use the given directory as Spryker project working directory.')
            ->addOption(
                static::TRANSLATION_ENGINE,
                'e',
                InputOption::VALUE_OPTIONAL,
                sprintf(
                    'The translation engine to be used for translation generation. Allowed values are %s or %s',
                    static::DEEPL_TRANSLATION_ENGINE,
                    static::CHATGPT_TRANSLATION_ENGINE,
                ),
                static::CHATGPT_TRANSLATION_ENGINE,
            );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->directory = $this->getDirectory($input->getOption(static::WORKING_DIR));
        $this->locales = $input->getArgument(static::ARGUMENT_LOCALES);
        $this->validateLocale($output);

        $translationEngine = $input->getOption(static::TRANSLATION_ENGINE);
        $this->getTranslationEngine($translationEngine, $output);

        return Command::SUCCESS;
    }

    /**
     * @param string|null $directory
     *
     * @return string
     */
    abstract protected function getDirectory(?string $directory): string;

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|void
     */
    protected function validateLocale(OutputInterface $output)
    {
        foreach ($this->locales as $locale) {
            if (!Locales::exists($locale)) {
                $output->writeln('<error>Invalid locale. For example es_ES, fr_FR, en_GB.</error>');

                return Command::FAILURE;
            }
        }

        if (!file_exists($this->directory)) {
            $output->writeln('<error>Directory not found!</error>');

            return Command::FAILURE;
        }
    }

    /**
     * @param string $translationEngine
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|void
     */
    protected function getTranslationEngine(string $translationEngine, OutputInterface $output)
    {
        switch ($translationEngine) {
            case static::CHATGPT_TRANSLATION_ENGINE:
                $this->translationEngine = $this->container->get(ChatGptTranslatorEngine::class);

                break;
            case static::DEEPL_TRANSLATION_ENGINE:
                $this->translationEngine = $this->container->get(DeepLTranslatorEngine::class);

                break;
            default:
                $output->writeln(sprintf(
                    '%s is not a valid translation engine. Available engines are: %s and %s',
                    $translationEngine,
                    static::CHATGPT_TRANSLATION_ENGINE,
                    static::DEEPL_TRANSLATION_ENGINE,
                ));

                return Command::FAILURE;
        }
    }
}
