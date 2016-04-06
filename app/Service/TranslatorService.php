<?php
namespace Memtext\Service;

use \Memtext\Mapper\TextMapper;
use \Memtext\Mapper\WordMapper;
use \Memtext\Helper\TextParser;
use \Memtext\Helper\TranslatorInterface as Translator;

class TranslatorService
{
    private $textMapper;
    private $textParser;
    private $wordMapper;
    private $translator;

    public function __construct(
        TextMapper $textMapper,
        WordMapper $wordMapper,
        TextParser $textParser,
        Translator $translator
    ) {
        $this->textMapper = $textMapper;
        $this->textParser = $textParser;
        $this->wordMapper = $wordMapper;
        $this->translator = $translator;
    }

    public function createVocabulary($text)
    {
        $words = $this->textParser->parse($text);
        $savedTranslations = $this->wordMapper->fetchArrayByEng($words);
        $missingTranslations = $this->getMissing($words, $savedTranslations);
        $newTranslations = $this->translator->translate($missingTranslations);
        $newWords = array_combine($missingTranslations, $newTranslations);
        $this->wordMapper->save($newWords);

        $words = array_merge($savedTranslations, $newWords);
        return $this->changeArrayFormat($words);
    }

    private function getMissing($words, $savedTranslations)
    {
        $missing = array_diff_key(array_flip($words), $savedTranslations);
        return array_keys($missing);
    }

    private function changeArrayFormat($words)
    {
        $result = [];
        foreach ($words as $eng => $rus) {
            $result[] = ['eng' => $eng, 'rus' => $rus, 'ignore' => false];
        }
        return $result;
    }
}
