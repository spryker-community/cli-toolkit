<?php

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use SprykerCommunity\Toolkit\Translator\TranslatorEngine\ChatGptTranslatorEngine;
use SprykerCommunity\Toolkit\Translator\TranslatorEngine\DeepLTranslatorEngine;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

$container = new ContainerBuilder();

// Shared
$container->register(StreamHandler::class, StreamHandler::class)
    ->setArguments([
        __DIR__ . '/../var/log/dev.log',
        Level::Debug,
    ]);

$container->register(Logger::class, Logger::class)
    ->setArguments(['spryker-toolkit'])
    ->addMethodCall('pushHandler', [
        new Reference(StreamHandler::class),
    ])->setPublic(true);

// Translator
$container->register(ChatGptTranslatorEngine::class, ChatGptTranslatorEngine::class)
    ->setArguments([
        new Reference(Logger::class),
        '%env(' . ChatGptTranslatorEngine::SPRYKER_TOOLKIT_CHATGPT_API_AUTH_KEY . ')%',
    ])->setPublic(true);

$container->register(DeepLTranslatorEngine::class, DeepLTranslatorEngine::class)
    ->setArguments([
        '%env(' . DeepLTranslatorEngine::SPRYKER_TOOLKIT_DEEPL_API_AUTH_KEY . ')%',
    ])->setPublic(true);

return $container;
