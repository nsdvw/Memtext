<?php
namespace Memtext\Model;

use Doctrine\Common\Collections\ArrayCollection;

class User
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $login;
    /**
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $saltedHash;
    /**
     * @var string
     */
    private $salt;

    /**
     * @var ArrayCollection
     */
    private $texts;

    public function __construct()
    {
        $this->texts = new ArrayCollection();
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
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getSaltedHash()
    {
        return $this->saltedHash;
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @param $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @param $saltedHash
     */
    public function setSaltedHash($saltedHash)
    {
        $this->saltedHash = $saltedHash;
    }

    /**
     * @param $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }
}
