<?php
namespace Memtext\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Connection;
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
        $references = $this->createDictionary($words, $text);

        $text->setWords($references);
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

    private function getReferences(array $ids)
    {
        $refs = [];
        $em = $this->entityManager;
        foreach ($ids as $id) {
            $refs[] = $em->getReference('Memtext:Word', $id);
        }
        return $refs;
    }

    private function createDictionary(array $words, Text $text)
    {
        $references = [];
        $stopWords = [];
        $ids = [];

        foreach ($words as $word) {
            $hits = $this->findHits([$word]);
            if (!$hits) {
                $stopWords[] = $word;
                continue;
            }
            $hits = $this->filterHits($hits, $text->getContent());
            $ids = array_merge($ids, $this->getHitsIds($hits));
        }
        $references = $this->getReferences(array_unique($ids));
        $fromDb = $this->findInDb($stopWords);

        return array_merge($references, $fromDb);
    }

    private function getHitsIds(array $hits)
    {
        $ids = [];
        foreach ($hits as $hit) {
            $ids[] = $hit['id'];
        }
        return array_unique($ids);
    }

    private function findInDb(array $stopWords)
    {
        $dql = "SELECT w.id FROM dictionary w WHERE w.keyword IN (?)";
        $conn = $this->entityManager->getConnection();
        $stmt = $conn->executeQuery($dql, [$stopWords], [Connection::PARAM_STR_ARRAY]);
        $hits = $stmt->fetchAll();
        $ids = $this->getHitsIds($hits);
        return $this->getReferences(array_unique($ids));
    }
}
