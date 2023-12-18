<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerCommunity\Toolkit\Translator\Commands;

use Monolog\Logger;
use SprykerCommunity\Toolkit\Translator\CategoryTranslator;
use SprykerCommunity\Toolkit\Translator\CmsBlockTranslator;
use SprykerCommunity\Toolkit\Translator\CmsPageTranslator;
use SprykerCommunity\Toolkit\Translator\ContentBannerTranslator;
use SprykerCommunity\Toolkit\Translator\Exception\TranslatorException;
use SprykerCommunity\Toolkit\Translator\GlossaryTranslator;
use SprykerCommunity\Toolkit\Translator\MerchantProfileTranslator;
use SprykerCommunity\Toolkit\Translator\NavigationNodeCategoryTranslator;
use SprykerCommunity\Toolkit\Translator\ProductAbstractTranslator;
use SprykerCommunity\Toolkit\Translator\ProductConcreteTranslator;
use SprykerCommunity\Toolkit\Translator\ProductDiscontinuedTranslator;
use SprykerCommunity\Toolkit\Translator\ProductLabelTranslator;
use SprykerCommunity\Toolkit\Translator\ProductManagementAttributeTranslator;
use SprykerCommunity\Toolkit\Translator\ProductOptionTranslator;
use SprykerCommunity\Toolkit\Translator\ProductSearchAttributeTranslator;
use SprykerCommunity\Toolkit\Translator\ProductSetTranslator;
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
     * @return array<\SprykerCommunity\Toolkit\Translator\AbstractYvesTranslator>
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
