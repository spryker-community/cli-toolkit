<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerCommunity\CliToolKit\Translator\Commands;

use Monolog\Logger;
use RuntimeException;
use SprykerCommunity\CliToolKit\Translator\AbstractYvesTranslator;
use SprykerCommunity\CliToolKit\Translator\CategoryTranslator;
use SprykerCommunity\CliToolKit\Translator\CmsBlockTranslator;
use SprykerCommunity\CliToolKit\Translator\CmsPageTranslator;
use SprykerCommunity\CliToolKit\Translator\ContentBannerTranslator;
use SprykerCommunity\CliToolKit\Translator\Exception\TranslatorException;
use SprykerCommunity\CliToolKit\Translator\GlossaryTranslator;
use SprykerCommunity\CliToolKit\Translator\MerchantProfileTranslator;
use SprykerCommunity\CliToolKit\Translator\NavigationNodeCategoryTranslator;
use SprykerCommunity\CliToolKit\Translator\ProductAbstractTranslator;
use SprykerCommunity\CliToolKit\Translator\ProductConcreteTranslator;
use SprykerCommunity\CliToolKit\Translator\ProductDiscontinuedTranslator;
use SprykerCommunity\CliToolKit\Translator\ProductLabelTranslator;
use SprykerCommunity\CliToolKit\Translator\ProductManagementAttributeTranslator;
use SprykerCommunity\CliToolKit\Translator\ProductOptionTranslator;
use SprykerCommunity\CliToolKit\Translator\ProductSearchAttributeTranslator;
use SprykerCommunity\CliToolKit\Translator\ProductSetTranslator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class YvesTranslationCommand extends AbstractTranslationCommand
{
    /**
     * @var string
     */
    protected const TRANSLATOR = 'translator';

    /**
     * @return void
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('translation:yves:generate')
            ->setDescription('Generate Yves translations to the specified target locale')
            ->addOption(
                static::TRANSLATOR,
                't',
                InputOption::VALUE_REQUIRED,
                'Comma separated list of translators to use',
                null,
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
        parent::execute($input, $output);

        $translators = $this->getTranslators();

        $this->validateTranslatorOption($translators, $input);

        $translators = $this->filterTranslatorsByTypes($translators, $this->getTranslatorFilterTypes($input));

        try {
            foreach ($translators as $translator) {
                $translator->setOutput($output);
                $translator->translate($this->directory, $this->locales);
            }
        } catch (TranslatorException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * @return array<\SprykerCommunity\CliToolKit\Translator\AbstractYvesTranslator>
     */
    protected function getTranslators(): array
    {
        $logger = $this->container->get(Logger::class);

        return [
            new CategoryTranslator($this->translationEngine, $logger),
            new CmsBlockTranslator($this->translationEngine, $logger),
            new CmsPageTranslator($this->translationEngine, $logger),
            new ContentBannerTranslator($this->translationEngine, $logger),
            new GlossaryTranslator($this->translationEngine, $logger),
            new MerchantProfileTranslator($this->translationEngine, $logger),
            new NavigationNodeCategoryTranslator($this->translationEngine, $logger),
            new ProductAbstractTranslator($this->translationEngine, $logger),
            new ProductConcreteTranslator($this->translationEngine, $logger),
            new ProductDiscontinuedTranslator($this->translationEngine, $logger),
            new ProductLabelTranslator($this->translationEngine, $logger),
            new ProductManagementAttributeTranslator($this->translationEngine, $logger),
            new ProductOptionTranslator($this->translationEngine, $logger),
            new ProductSearchAttributeTranslator($this->translationEngine, $logger),
            new ProductSetTranslator($this->translationEngine, $logger),
        ];
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

    /**
     * @param array<\SprykerCommunity\CliToolKit\Translator\AbstractYvesTranslator> $availableTranslators
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    protected function validateTranslatorOption(array $availableTranslators, InputInterface $input): void
    {
        $translatorTypes = $this->getTranslatorFilterTypes($input);
        $availableTranslatorTypes = array_map(
            fn (AbstractYvesTranslator $translator): string => $translator->getType(),
            $availableTranslators,
        );

        if (!count($translatorTypes)) {
            return;
        }

        $invalidTranslatorTypes = array_diff($translatorTypes, $availableTranslatorTypes);

        if (count($invalidTranslatorTypes)) {
            throw new RuntimeException(sprintf(
                'Invalid translator types "%s". Available types: "%s"',
                implode(', ', $invalidTranslatorTypes),
                implode(', ', $availableTranslatorTypes),
            ));
        }
    }

    /**
     * @param array<\SprykerCommunity\CliToolKit\Translator\AbstractYvesTranslator> $translators
     * @param array<string> $types
     *
     * @return array<\SprykerCommunity\CliToolKit\Translator\AbstractYvesTranslator>
     */
    protected function filterTranslatorsByTypes(array $translators, array $types): array
    {
        if (!count($types)) {
            return $translators;
        }

        return array_filter(
            $this->getTranslators(),
            fn (AbstractYvesTranslator $translator): bool => in_array($translator->getType(), $types),
        );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return array<string>
     */
    protected function getTranslatorFilterTypes(InputInterface $input): array
    {
        $optionTranslators = $input->getOption(static::TRANSLATOR);

        if (!$optionTranslators) {
            return [];
        }

        return explode(',', $optionTranslators);
    }
}
