<?php
namespace Memtext\Service;

use \Memtext\Mapper\TextMapper;
use \Memtext\Mapper\WordMapper;
use \Memtext\Helper\TextParser;
use \Memtext\Helper\TranslatorInterface as Translator;
use \Memtext\Redis\RedisAdapterInterface as Redis;

class TranslatorService
{
    private $textMapper;
    private $textParser;
    private $redisClient;
    private $wordMapper;

    public function __construct(
        TextMapper $textMapper,
        WordMapper $wordMapper,
        TextParser $textParser,
        Translator $translator,
        Redis $client
    ) {
        $this->textMapper = $textMapper;
        $this->textParser = $textParser;
        $this->wordMapper = $wordMapper;
        $this->translator = $translator;
        $this->client = $client;
    }

    public function createVocabulary($text)
    {
        $words = $this->textParser->parse($text);
        $savedTranslations = $this->wordMapper->fetchArrayByEng($words);
        $missingTranslations = $this->getMissing($words, $savedTranslations);
        $newTranslations = $this->translator->translate($missingTranslations);
        $newWords = array_combine($missingTranslations, $newTranslations);
        $this->wordMapper->save($newWords);

        return array_merge($savedTranslations, $newWords);
    }

    public function saveToRedis($textId, array $words)
    {
        $client = $this->client;
        $key = $client->getPrefix() . ":{$textId}";
        $client->hmset($key, $words);
    }

    private function getMissing($words, $savedTranslations)
    {
        $missing = array_diff_key(array_flip($words), $savedTranslations);
        return array_keys($missing);
    }
}
