<?php
namespace Memtext\Form;

use Memtext\Model\User;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Memtext\Helper\HashGenerator;

class LoginForm extends AbstractForm
{
    const USER_NOT_FOUND = 'Пользователь не найден';
    const WRONG_PASSWORD = 'Неправильный пароль';

    private $user;

    public $email;
    public $password;
    public $remember;

    public function __construct(Request $request)
    {
        $loginData = $request->getParsedBody()['loginForm'];
        $this->email = isset($loginData['email']) ? $loginData['email'] : null;
        $this->password =
            isset($loginData['password']) ? $loginData['password'] : null;
        $this->remember = isset($loginData['remember']);
    }

    public function validatePassword(User $user = null)
    {
        if ($user == null) {
            $this->errorMessage = self::USER_NOT_FOUND;
            return false;
        } elseif (
            $user->getSaltedHash() !==
            HashGenerator::generateHash($user->getSalt(), $this->password)
        ) {
            $this->errorMessage = self::WRONG_PASSWORD;
            return false;
        }
        $this->user = $user;
        return true;
    }

    protected function rules()
    {
        return [
            'email' =>
                ['notEmpty' => true, 'isEmail' => true, 'maxLength' => 50],
            'password' =>
                ['notEmpty' => true, 'minLength' => 5, 'maxLength' => 50],
        ];
    }

    public function getUser()
    {
        return $this->user;
    }
}
