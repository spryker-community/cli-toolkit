<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerCommunity\CliToolKit\Translator\TranslatorEngine;

use Locale;
use OpenAI;
use OpenAI\Client;
use Psr\Log\LoggerInterface;

class ChatGptTranslatorEngine implements TranslatorEngineInterface
{
    /**
     * @var string
     */
    public const CLI_TOOLKIT_CHATGPT_API_AUTH_KEY = 'CLI_TOOLKIT_CHATGPT_API_AUTH_KEY';

    /**
     * @var string
     */
    protected const PROMPT_LOCALE = 'en';

    /**
     * @var string
     */
    protected const CHATGPT_PROMPT = 'Support me in translating %s texts to %s for an online shop, ensuring native speaker fluency. Generate accurate and contextually fitting translations to enhance the user experience. The texts to be translated may contain URLs, URL paths, HTML, unicode characters or some word enclosed by the character "%%", please don\'t translate them. If the text only contains a relative URL starting by /%s/ please replace it by /%s/. Do not split translations for HTML <p> elements. IMPORTANT: ONLY RETURN THE TRANSLATED TEXT AND NOTHING ELSE.';

    /**
     * @var string
     */
    protected const CHATGPT_MESSAGE_ROLE_KEY = 'role';

    /**
     * @var string
     */
    protected const CHATGPT_MESSAGE_CONTENT_KEY = 'content';

    /**
     * @var string
     */
    protected const CHATGPT_MESSAGE_ROLE_SYSTEM_VALUE = 'system';

    /**
     * @var string
     */
    protected const CHATGPT_ENGINE = 'gpt-3.5-turbo';

    protected Client $client;

    private LoggerInterface $logger;

    /**
     * @var array<string, array<string, string>>
     */
    private array $cache;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param string $apiKey
     */
    public function __construct(LoggerInterface $logger, string $apiKey)
    {
        $this->logger = $logger;
        $this->client = OpenAI::client($apiKey);
    }

    /**
     * @param string $text
     * @param string $targetLang
     * @param string $sourceLang
     *
     * @return string
     */
    public function translate(string $text, string $targetLang, string $sourceLang): string
    {
        if (isset($this->cache[$targetLang][$text])) {
            return $this->cache[$targetLang][$text];
        }

        $prompt = sprintf(
            static::CHATGPT_PROMPT,
            Locale::getDisplayName($sourceLang, static::PROMPT_LOCALE),
            Locale::getDisplayName($targetLang, static::PROMPT_LOCALE),
            substr($sourceLang, 0, 2),
            substr($targetLang, 0, 2),
        );
        $cleanText = preg_replace('/\s+/m', ' ', $text) ?? '';
        $message = $prompt . '\n###Text:\n ' . $cleanText . '\n###';

        $this->logger->debug('New translation request', [
            'message' => $message,
        ]);
        $result = $this->client->chat()->create([
            'model' => static::CHATGPT_ENGINE,
            'temperature' => 0,
            'messages' => [
                [
                    static::CHATGPT_MESSAGE_ROLE_KEY => static::CHATGPT_MESSAGE_ROLE_SYSTEM_VALUE,
                    static::CHATGPT_MESSAGE_CONTENT_KEY => $message,
                ],
            ],
        ]);

        $matches = [];
        $translation = trim($result->choices[0]->message->content ?? '');
        $translation = $this->sanitizeOutput($translation);

        $this->cache[$targetLang][$text] = $translation;

        return $this->cache[$targetLang][$text];
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return static::CHATGPT_ENGINE;
    }

    /**
     * @param string $output
     *
     * @return string
     */
    private function sanitizeOutput(string $output): string
    {
        preg_replace([
            '/\\\\n###/',
            '/###Text:/',
            '/###Translation:/',
            '/\\n###/',
        ], '', $output) ?? '';

        return $output;
    }
}
