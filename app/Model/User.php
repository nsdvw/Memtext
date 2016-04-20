<?php
namespace Memtext\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class User
 * @package Memtext\Model
 *
 * @Entity
 * @Table(name="user")
 */
class User
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
    private $login;

    /**
     * @var string
     *
     * @Column(type="string")
     */
    private $email;

    /**
     * @var string
     *
     * @Column(name="salted_hash", type="string")
     */
    private $saltedHash;

    /**
     * @var string
     *
     * @Column(type="string")
     */
    private $salt;

    /**
     * @var ArrayCollection
     *
     * @OneToMany(targetEntity="Text", mappedBy="author")
     */
    private $texts;

    /**
     * @var ArrayCollection
     *
     * @ManyToMany(targetEntity="Word")
     * @JoinTable(
     *   name="user_dictionary",
     *   joinColumns={@JoinColumn(name="user_id", referencedColumnName="id")},
     *   inverseJoinColumns={@JoinColumn(name="word_id", referencedColumnName="id")}
     * )
     */
    private $ignoredWords;

    public function __construct()
    {
        $this->texts = new ArrayCollection();
        $this->ignoredWords = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getIgnoredWords()
    {
        return $this->ignoredWords;
    }

    /**
     * @param array $words
     */
    public function ignore(array $words)
    {
        foreach ($words as $word) {
            $this->ignoredWords[] = $word;
        }
    }

    /**
     * @param array $texts
     */
    public function attachTexts(array $texts)
    {
        foreach ($texts as $text) {
            $this->texts[] = $text;
        }
    }

    /**
     * @param Text $text
     */
    public function attachText(Text $text)
    {
        $this->texts[] = $text;
    }

    /**
     * @return ArrayCollection
     */
    public function getTexts()
    {
        return $this->texts;
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
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getSaltedHash()
    {
        return $this->saltedHash;
    }

    /**
     * @param $saltedHash
     */
    public function setSaltedHash($saltedHash)
    {
        $this->saltedHash = $saltedHash;
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }
}
