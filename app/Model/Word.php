<?php
namespace Memtext\Model;

/**
 * Class Dictionary
 * @package Memtext\Model
 *
 * @Entity
 * @Table(name="dictionary")
 */
class Word
{
    /**
     * @var int
     *
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Column(type="string")
     */
    private $keyword;

    /**
     * @var string
     *
     * @Column(type="text")
     */
    private $definition;

    /**
     * @var string
     *
     * @Column(type="string")
     */
    private $type;



    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getKeyword()
    {
        return $this->keyword;
    }

    /**
     * @param string $keyword
     */
    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;
    }

    /**
     * @return string
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * @param string $definition
     */
    public function setDefinition($definition)
    {
        $this->definition = $definition;
    }
}
