<?php
namespace Memtext\Form;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Memtext\Model\User;
use \Memtext\Helper\HashGenerator;

class RegisterForm extends AbstractForm
{
    const LOGIN_OCCUPIED = 'Имя занято, попробуйте другое';
    const MAIL_OCCUPIED = 'С этой почты уже была регистрация';
    const REPEAT_PASSWORD = 'Пароли не совпадают';

    public $login;
    public $email;
    public $password;
    public $repeatPassword;
    public $remember;

    private $user;

    public function __construct(Request $request)
    {
        $regData = isset($request->getParsedBody()['registerForm'])
                   ? $request->getParsedBody()['registerForm'] : null;
        $this->login = isset($regData['login']) ? $regData['login'] : null;
        $this->email = isset($regData['email']) ? $regData['email'] : null;
        $this->password = isset($regData['password']) ? $regData['password'] : null;
        $this->repeatPassword = 
            isset($regData['repeatPassword']) ? $regData['repeatPassword'] : null;
        $this->remember = isset($regData['remember']);

        $this->createUser();
    }

    public function validateUniqueEmail($user = null)
    {
        if (!$user) {
            return true;
        }
        $this->errorMessage = self::MAIL_OCCUPIED;
        return false;
    }

    public function validateUniqueLogin($user = null)
    {
        if (!$user) {
            return true;
        }
        $this->errorMessage = self::LOGIN_OCCUPIED;
        return false;
    }

    public function getUser()
    {
        return $this->user;
    }

    protected function validateEquals($field, $toWhat)
    {
        if ($this->$field === $this->$toWhat) {
            return true;
        }
        $this->errorMessage = self::REPEAT_PASSWORD;
        return false;
    }

    private function createUser()
    {
        $user = new User;
        $user->login = $this->login;
        $user->email = $this->email;
        $user->salt = HashGenerator::generateSalt();
        $user->saltedHash = HashGenerator::generateHash($user->salt, $this->password);
        $this->user = $user;
    }

    protected function rules()
    {
        return [
            'login' =>
                ['notEmpty'=>true, 'maxLength'=>30, 'minLength'=>4],
            'email' =>
                ['notEmpty'=>true, 'maxLength'=>255, 'isEmail'=>true],
            'password' =>
                ['notEmpty'=>true, 'maxLength'=>50, 'minLength'=>5],
            'repeatPassword' =>
                ['equals' => 'password'],
        ];
    }
}
