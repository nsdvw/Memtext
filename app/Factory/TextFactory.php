<?php
namespace Memtext\Factory;

use Memtext\Model\Text;

class TextFactory extends AbstractFactory
{
    public function create($fields = [])
    {
        $text = new Text;
        foreach ($fields as $fieldName => $fieldValue) {
            if (!in_array($fieldName, $this->fieldList)) {
                continue;
            }
            $text->$fieldName = $fieldValue;
        }
        return $text;
    }
}
