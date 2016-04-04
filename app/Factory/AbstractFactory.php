<?php
namespace Memtext\Factory;

class AbstractFactory
{
    protected $fieldList;

    public function __construct(array $fieldList)
    {
        $this->fieldList = $fieldList;
    }

    public function setFieldList(array $fieldList)
    {
        $this->fieldList = $fieldList;
    }
}
