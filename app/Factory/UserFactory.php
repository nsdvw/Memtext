<?php
namespace Memtext\Factory;

use Memtext\Model\User;

class UserFactory
{
    private $fieldList;

    public function __construct(array $fieldList)
    {
        $this->fieldList = $fieldList;
    }

    public function setFieldList(array $fieldList)
    {
        $this->fieldList = $fieldList;
    }

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
