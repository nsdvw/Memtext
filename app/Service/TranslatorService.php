<?php
namespace Memtext\Service;

use \Memtext\Mapper\TextMapper;

class TranslatorService
{
    private $textMapper;
    private $redisClient;

    public function __construct(TextMapper $textMapper/*, $redisClient*/)
    {
        $this->textMapper = $textMapper;
        // $this->redisClient = $redisClient;
    }

    public function parseText($text)
    {
        $words = preg_split('/\W+/ui', $text, -1, \PREG_SPLIT_NO_EMPTY);
        $words = $this->deleteLongWords($words);
        $words = array_unique($words);
        $words = $this->toLowerCase($words);
        return $words;
    }

    private function deleteLongWords(array $words, $maxLength = 40)
    {
        $words = array_filter($words, function ($word) use ($maxLength) {
            return mb_strlen($word, 'UTF-8') <= $maxLength;
        });
        return $words;
    }

    private function toLowerCase(array $words)
    {
        array_walk($words, function (&$word) {
            $word = mb_convert_case($word, MB_CASE_LOWER);
        });
        return $words;
    }
}
