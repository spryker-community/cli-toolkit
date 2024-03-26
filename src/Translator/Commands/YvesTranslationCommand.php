<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerCommunity\CliToolKit\Translator\Commands;

use Monolog\Logger;
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
use Symfony\Component\Console\Output\OutputInterface;

class YvesTranslationCommand extends AbstractTranslationCommand
{
    /**
     * @return void
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('translation:yves:generate')
            ->setDescription('Generate Yves translations to the specified target locale');
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
            foreach ($this->getTranslators() as $translator) {
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
}
