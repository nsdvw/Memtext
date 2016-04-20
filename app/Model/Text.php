<?php
namespace Memtext\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Text
 * @package Memtext\Model
 *
 * @Entity
 * @Table(name="text")
 */
class Text
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
     * @Column(type="text")
     */
    private $content;

    /**
     * @var string
     *
     * @Column(type="string")
     */
    private $title;

    /**
     * @var User
     *
     * @ManyToOne(targetEntity="User", inversedBy="texts")
     */
    private $author;

    /**
     * @var ArrayCollection
     *
     * @ManyToMany(targetEntity="Word")
     * @JoinTable(
     *   name="text_dictionary",
     *   joinColumns={@JoinColumn(name="text_id", referencedColumnName="id")}),
     *   inverseJoinColumns={@JoinColumn(name="word_id", referencedColumnName="id")}
     * )
     */
    private $words;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->words = new ArrayCollection();
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
    public function getWords()
    {
        return $this->words;
    }

    /**
     * @return array
     */
    public function getShortWordsArray()
    {
        $entities = $this->words->toArray();
        $words = [];
        foreach ($entities as $entity) {
            if ($entity->getType() != 'short') {
                continue;
            }
            $words[$entity->getKeyword()] = $entity->getDefinition();
        }
        return $words;
    }

    /**
     * @param array $words
     */
    public function setWords(array $words)
    {
        foreach ($words as $word) {
            $this->words[] = $word;
        }
    }
}
