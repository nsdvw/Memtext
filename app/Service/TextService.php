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
        $words = $this->parseText($text);

        $hits = $this->findHits($words);

        $hits = $this->filterHits($hits, $text->getContent());

        $refs = $this->getReferences($hits);

        $text->setWords($refs);

        $this->entityManager->persist($text);
        $this->entityManager->flush();
    }

    public function getUserTextCount($userId)
    {
        $em = $this->entityManager;
        $qb = $em->createQueryBuilder();
        $query = $qb->select('COUNT(t)')
            ->from('Memtext:Text', 't')
            ->where('t.author=:id')
            ->setParameter('id', $userId)
            ->getQuery();
        return $query->getSingleScalarResult();
    }

    public function getTextWithWords($textId)
    {
        $dql = "SELECT t, w FROM Memtext:Text t JOIN t.words w WHERE t.id=?1";
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter(1, $textId);
        return $query->getResult()[0];
    }

    private function parseText(Text $text)
    {
        $textContent = strip_tags($text->getContent());
        return $this->parser->parse($textContent);
    }

    private function findHits(array $words)
    {
        return $this->sphinxService->find($words);
    }

    private function filterHits(array $hits, $textContent)
    {
        $filter = function ($word) use ($textContent) {
            if (strpos($textContent, $word['word']) === false) {
                return false;
            } else {
                return true;
            }
        };

        return array_filter($hits, $filter);
    }

    private function getReferences(array $hits)
    {
        $refs = [];
        $em = $this->entityManager;
        foreach ($hits as $hit) {
            $refs[] = $em->getPartialReference('Memtext:Word', $hit['id']);
        }
        return $refs;
    }
}
