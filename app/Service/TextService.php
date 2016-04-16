<?php
namespace Memtext\Service;

use Doctrine\ORM\EntityManager;
use Memtext\Helper\TextParser;
use Memtext\Model\Text;

class TextService
{
    private $parser;
    private $entityManager;
    private $sphinxService;

    public function __construct(
        TextParser $parser,
        EntityManager $entityManager,
        SphinxService $sphinxService
    ) {
        $this->parser = $parser;
        $this->entityManager = $entityManager;
        $this->sphinxService = $sphinxService;
    }

    public function saveWithDictionary(Text $text)
    {
        $textContent = strip_tags($text->getContent());
        $words = $this->parser->parse($textContent);

        $shortIds = $this->sphinxService->findInShortDict($words);
        $fullIds = $this->sphinxService->findInFullDict($words);
        $shortFounded = $this->findShortWords($shortIds);
        $fullFounded = $this->findFullWords($fullIds);

        $filter = function ($word) use ($textContent) {
            if (strpos($textContent, $word->getKeyword()) === false) {
                return false;
            } else {
                return true;
            }
        };
        $shortFounded = array_filter($shortFounded, $filter);
        $fullFounded = array_filter($fullFounded, $filter);

        $text->setShortdicts($shortFounded);
        $text->setFulldicts($fullFounded);
        $this->entityManager->persist($text);
        $this->entityManager->flush();
    }

    private function findShortWords(array $ids)
    {
        $entity = '\Memtext\Model\ShortDict';
        $repo = $this->entityManager->getRepository($entity);
        return $repo->findById($ids);
    }

    private function findFullWords(array $ids)
    {
        $entity = '\Memtext\Model\FullDict';
        $repo = $this->entityManager->getRepository($entity);
        return $repo->findById($ids);
    }
}
