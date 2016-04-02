<?php
namespace Memtext\Form;

use \Psr\Http\Message\ServerRequestInterface as Request;

class TextForm extends AbstractForm
{
    public $content;

    public function __construct(Request $request)
    {
        $textFormData = $request->getParsedBody()['textForm'];
        $this->content = isset($textFormData['content'])
                         ? $textFormData['content'] : null;
    }

    protected function rules()
    {
        return [
            'content' => ['notEmpty' => true, 'maxLength' => 65000],
        ];
    }
}
