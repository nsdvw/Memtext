<?php
namespace Memtext\Helper;

class TextParser
{
    private $words;
    private $text;

    public function parse($text)
    {
        $this->text = $text;

        $this
            ->splitToWords()
            ->deleteLongWords()
            ->filterUnique()
            ->toLowerCase();

        return array_values($this->words);
    }

    private function splitToWords()
    {
        $this->words = preg_split(
            '/(\'s|\'t)?\W+/ui',
            $this->text,
            -1,
            \PREG_SPLIT_NO_EMPTY
        );
        return $this;
    }

    private function deleteLongWords($maxLength = 50)
    {
        $callback = function ($word) use ($maxLength) {
            return mb_strlen($word, 'UTF-8') <= $maxLength;
        };
        $this->words = array_filter($this->words, $callback);
        return $this;
    }

    private function filterUnique()
    {
        $this->words = array_unique($this->words);
        return $this;
    }

    private function toLowerCase()
    {
        array_walk($this->words, function (&$word) {
            $word = mb_convert_case($word, MB_CASE_LOWER);
        });
        return $this;
    }
}
