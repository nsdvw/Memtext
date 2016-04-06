<?php
namespace Memtext\Form;

use \Psr\Http\Message\ServerRequestInterface as Request;

class TextForm extends AbstractForm
{
    public $content;
    public $title;

    public function __construct(Request $request, \HTMLPurifier $purifier)
    {
        $textFormData = $request->getParsedBody()['textForm'];
        $this->content = isset($textFormData['content'])
                         ? $purifier->purify($textFormData['content']) : null;
        $this->title = isset($textFormData['title'])
                         ? $textFormData['title'] : null;
    }

    protected function rules()
    {
        return [
            'content' => ['notEmpty' => true, 'maxLength' => 65000],
            'title' => ['notEmpty' => true, 'maxLength' => 255],
        ];
    }
}
