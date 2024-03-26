<?php

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use SprykerCommunity\CliToolKit\Translator\TranslatorEngine\ChatGptTranslatorEngine;
use SprykerCommunity\CliToolKit\Translator\TranslatorEngine\DeepLTranslatorEngine;
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
    ->setArguments(['cli-toolkit'])
    ->addMethodCall('pushHandler', [
        new Reference(StreamHandler::class),
    ])->setPublic(true);

// Translator
$container->register(ChatGptTranslatorEngine::class, ChatGptTranslatorEngine::class)
    ->setArguments([
        new Reference(Logger::class),
        '%env(' . ChatGptTranslatorEngine::CLI_TOOLKIT_CHATGPT_API_AUTH_KEY . ')%',
    ])->setPublic(true);

$container->register(DeepLTranslatorEngine::class, DeepLTranslatorEngine::class)
    ->setArguments([
        '%env(' . DeepLTranslatorEngine::CLI_TOOLKIT_DEEPL_API_AUTH_KEY . ')%',
    ])->setPublic(true);

return $container;
