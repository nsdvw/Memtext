<?php
namespace Memtext\Form;

use Psr\Http\Message\ServerRequestInterface as Request;

class LogoutForm extends AbstractForm
{
    public $csrf_token;

    public function __construct(Request $request)
    {
        $formData = $request->getParsedBody()['logoutForm'];
        $this->csrf_token = isset($formData['csrf_token']) ?
            $formData['csrf_token'] : null;
    }
}
