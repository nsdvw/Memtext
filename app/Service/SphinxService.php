<?php
namespace Memtext\Service;

use Memtext\Mapper\SphinxMapper;

/**
 * Class SphinxService
 * @package Memtext\Service
 */
class SphinxService
{
    /**
     * @var SphinxMapper
     */
    private $mapper;

    /**
     * @var string
     */
    private $indexName;

    /**
     * @param SphinxMapper $mapper
     * @param string $indexName
     */
    public function __construct(
        SphinxMapper $mapper,
        $indexName
    ) {
        $this->mapper = $mapper;
        $this->indexName = $indexName;
    }

    public function find(array $words)
    {
        return $this->mapper->find($words, $this->indexName);
    }
}
