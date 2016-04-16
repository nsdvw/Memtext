<?php
namespace Memtext\Service;

use Memtext\Mapper\SphinxMapper;

class SphinxService
{
    /**
     * @var SphinxMapper
     */
    private $mapper;
    /**
     * @var string
     */
    private $shortDictIxName;
    /**
     * @var string
     */
    private $fullDictIxName;

    /**
     * @param SphinxMapper $mapper
     */
    public function __construct(
        SphinxMapper $mapper,
        $shortDictIxName,
        $fullDictIxName
    ) {
        $this->mapper = $mapper;
        $this->shortDictIxName = $shortDictIxName;
        $this->fullDictIxName = $fullDictIxName;
    }

    public function findInShortDict(array $words)
    {
        return $this->mapper->find($words, $this->shortDictIxName);
    }

    public function findInFullDict(array $words)
    {
        return $this->mapper->find($words, $this->fullDictIxName);
    }
}
