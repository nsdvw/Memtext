<?php
namespace Memtext\Model;

class User extends AbstractModel
{
    private $id;
    private $login;
    private $email;
    private $saltedHash;
    private $salt;

    public function getId()
    {
        return $this->id;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getSaltedHash()
    {
        return $this->saltedHash;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setLogin($login)
    {
        $this->login = $login;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setSaltedHash($saltedHash)
    {
        $this->saltedHash = $saltedHash;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;
    }
}
