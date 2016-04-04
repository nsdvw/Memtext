<?php
namespace Memtext\Factory;

use Memtext\Model\User;

class UserFactory extends AbstractFactory
{
    public function create($fields = [])
    {
        $user = new User;
        foreach ($fields as $fieldName => $fieldValue) {
            if (!in_array($fieldName, $this->fieldList)) {
                continue;
            }
            $user->$fieldName = $fieldValue;
        }
        return $user;
    }
}
