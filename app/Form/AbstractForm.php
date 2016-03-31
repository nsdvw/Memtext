<?php
namespace Memtext\Form;

abstract class AbstractForm
{
    public $errorMessage;

    public function validate()
    {
        $rules = $this->rules();
        foreach ($rules as $field=>$list) {
            foreach ($list as $rule=>$attributes) {
                $validator = 'validate' . ucfirst($rule);
                if ( !$this->$validator($field, $attributes) ) {
                    return false;
                }
            }
        }
        return true;
    }

    protected function validateNotEmpty($field, $flag = true)
    {
        if (empty($this->$field)) {
            $this->errorMessage = "$field не может быть пустым";
            return false;
        }
        return true;
    }

    protected function validateMaxLength($field, $maxLength)
    {
        if (mb_strlen($this->$field) > $maxLength) {
            $this->errorMessage = "$field должен быть не длиннее $maxLength символов";
            return false;
        }
        return true;
    }

    protected function validateMinLength($field, $minLength)
    {
        if (mb_strlen($this->$field) < $minLength) {
            $this->errorMessage = "$field должен быть не короче $minLength символов";
            return false;
        }
        return true;
    }

    protected function validateIsEmail($field, $flag = true)
    {
        $regExp = '/^[^@\s]+@[^@\s]+\.[^@\s]+$/ui';
        if (!preg_match($regExp, $this->$field)) {
            $this->errorMessage = 'Некорректный адрес электронной почты';
            return false;
        }
        return true;
    }
}