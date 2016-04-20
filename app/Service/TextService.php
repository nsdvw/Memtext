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

        $wordsIds = $this->sphinxService->find($words);
        $repo = $this->entityManager->getRepository('Memtext:Word');
        $words = $repo->findById($wordsIds);

        $filter = function ($word) use ($textContent) {
            if (strpos($textContent, $word->getKeyword()) === false) {
                return false;
            } else {
                return true;
            }
        };
        $words = array_filter($words, $filter);

        $text->setWords($words);
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
}
