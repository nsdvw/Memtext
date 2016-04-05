<?php
namespace Memtext\Auth;

use \Memtext\Mapper\UserMapper;
use \Memtext\Form\LoginForm;
use \Memtext\Form\RegisterForm;
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

    public function validateRegisterForm(RegisterForm $form)
    {
        if (!$form->validate()) {
            return false;
        }
        $foundedUser = $this->mapper->findByEmail($form->email);
        if (!$form->validateUniqueEmail($foundedUser)) {
            return false;
        }
        $foundedUser = $this->mapper->findByLogin($form->login);
        return $form->validateUniqueLogin($foundedUser);
    }

    public function login(User $user, $remember = true, $time = 604800)
    {
        $expires = $remember ? time() + $time : 0;
        $path = '/';
        setcookie('id', $user->id, $expires, $path);
        setcookie('hash', $user->saltedHash, $expires, $path);
        $this->loggedUser = $user;
    }

    public function logout()
    {
        setcookie('id', '', time() - 3600);
        setcookie('hash', '', time() - 3600);
        $this->loggedUser = null;
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

    public function isOwner($textAuthorId)
    {
        if (intval($this->getUserId()) === intval($textAuthorId)) {
            return true;
        }
        return false;
    }
}
