<?php
namespace Memtext\Service;

use \Memtext\Mapper\TextMapper;
use \Memtext\Helper\TextParser;

class TranslatorService
{
    private $textMapper;
    private $textParser;
    private $redisClient;
    private $wordMapper;

    public function __construct(
        TextMapper $textMapper,
        WordMapper $wordMapper,
        TextParser $textParser
        /*, $redisClient*/
    ) {
        $this->textMapper = $textMapper;
        $this->textParser = $textParser;
        $this->wordMapper = $wordMapper;
        // $this->redisClient = $redisClient;
    }

    public function createVocabulary($text)
    {
        $words = $this->textParser->parse($text);

        return $words;
    }

    
}
