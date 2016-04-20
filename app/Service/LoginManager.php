<?php
namespace Memtext\Service;

use Doctrine\ORM\EntityRepository;
use Memtext\Form\LoginForm;
use Memtext\Form\RegisterForm;
use Memtext\Model\User;

class LoginManager
{
    private $repo;
    private $loggedUser;
    private $token;

    public function __construct(EntityRepository $repo, $token) {
        $this->repo = $repo;
        $this->loggedUser = $this->getLoggedUser();
        $this->token = $token;
    }

    public function getLoggedUser()
    {
        $id = isset($_COOKIE['id']) ? intval($_COOKIE['id']) : null;
        $hash = isset($_COOKIE['hash']) ? $_COOKIE['hash'] : null;
        if (!$id or !$hash) {
            return null;
        }
        $user = $this->repo->find($id);
        if (!$user) {
            return null;
        } elseif ($user->getSaltedHash() != $hash) {
            return null;
        }
        return $user;
    }

    public function validateLoginForm(LoginForm $form)
    {
        if (!$form->validate()) {
            return false;
        }
        $user = $this->repo->findOneBy( ['email'=>$form->email] );
        return $form->validatePassword($user);
    }

    public function validateRegisterForm(RegisterForm $form)
    {
        if (!$form->validate()) {
            return false;
        }
        $foundedUser = $this->repo->findOneBy( ['email'=>$form->email] );
        if (!$form->validateUniqueEmail($foundedUser)) {
            return false;
        }
        $foundedUser = $this->repo->findOneBy( ['login'=>$form->login] );
        return $form->validateUniqueLogin($foundedUser);
    }

    public function login(User $user, $remember = true, $time = 604800)
    {
        $expires = $remember ? time() + $time : 0;
        $path = '/';
        setcookie('id', $user->getId(), $expires, $path);
        setcookie('hash', $user->getSaltedHash(), $expires, $path);
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
            return $this->loggedUser->getId();
        }
        return null;
    }

    public function getUserLogin()
    {
        if ($this->isLogged()) {
            return $this->loggedUser->getLogin();
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

    public function checkToken($formToken)
    {
        return $this->token === $formToken;
    }

    public function getToken()
    {
        return $this->token;
    }
}
