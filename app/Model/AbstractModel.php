<?php
namespace Memtext\Model;

class AbstractModel
{
    public function __get($property)
    {
        $getter = 'get' . ucfirst($property);
        return $this->{$getter}();
    }

    public function __set($property, $value)
    {
        $setter = 'set' . ucFirst($property);
        $this->{$setter}($value);
    }
}
