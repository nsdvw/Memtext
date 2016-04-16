<?php
namespace Memtext\Model;

use Doctrine\Common\Collections\ArrayCollection;

class Text
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $content;
    /**
     * @var string
     */
    private $title;
    /**
     * @var User
     */
    private $author;

    /**
     * @var ArrayCollection
     */
    private $shortdicts;
    /**
     * @var ArrayCollection
     */
    private $fulldicts;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->shortdicts = new ArrayCollection();
        $this->fulldicts = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param User $author
     */
    public function setAuthor(User $author)
    {
        $author->attachText($this);
        $this->author = $author;
    }

    /**
     * @return ArrayCollection
     */
    public function getShortdicts()
    {
        return $this->shortdicts;
    }

    /**
     * @return array
     */
    public function getShortdictsArray()
    {
        $entities = $this->shortdicts->toArray();
        $words = [];
        foreach ($entities as $entity) {
            $words[$entity->getKeyword()] = $entity->getDefinition();
        }
        return $words;
    }

    /**
     * @param array $words
     */
    public function setShortdicts(array $words)
    {
        foreach ($words as $word) {
            $this->shortdicts[] = $word;
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getFulldicts()
    {
        return $this->fulldicts;
    }

    /**
     * @param array $words
     */
    public function setFulldicts(array $words)
    {
        foreach ($words as $word) {
            $this->fulldicts[] = $word;
        }
    }
}
