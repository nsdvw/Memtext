<?php
namespace Memtext\Auth;

use \Memtext\Mapper\UserMapper;
use \Memtext\Form\LoginForm;
use \Memtext\Model\User;

class LoginManager
{
    private $mapper;
    private $loggedUser;

    public function __construct(UserMapper $mapper) {
        $this->mapper = $mapper;
        $this->loggedUser = $this->getLoggedUser();
    }

    public function getLoggedUser()
    {
        $id = isset($_COOKIE['id']) ? intval($_COOKIE['id']) : null;
        $hash = isset($_COOKIE['hash']) ? intval($_COOKIE['hash']) : null;
        if (!$id or !$hash) {
            return null;
        }
        $user = $this->mapper->findById($id);
        if ($user->saltedHash != $hash) {
            return null;
        }
        return $user;
    }

    public function validateLoginForm(LoginForm $form)
    {
        if (!$form->validate()) {
            return false;
        }
        $user = $this->mapper->findByEmail($form->email);
        return $form->validatePassword($user);
    }

    public function authorizeUser(User $user, $remember = true, $time = 604800)
    {
        $expires = $remember ? time() + $time : 0;
        $path = '/';
        setcookie('id', $user->id, $expires, $path);
        setcookie('hash', $user->hash, $expires, $path);
        $this->loggedUser = $user;
    }

    public function getUserID()
    {
        if ($this->isLogged()) {
            return $this->loggedUser->id;
        }
        return null;
    }

    public function getUserLogin()
    {
        if ($this->isLogged()) {
            return $this->loggedUser->login;
        }
        return null;
    }

    public function isLogged()
    {
        if ($this->loggedUser !== null) {
            return true;
        }
        return false;
    }
}
