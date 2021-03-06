<?php
namespace Memtext\Helper;

class HashGenerator
{
    const SALT_LENGTH = 40;
    
    public static function getCharacters()
    {
        return '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
                . '~`!@#$%^&*()-=+_][{}|?><';
    }

    public static function generateSalt()
    {
        $salt = '';
        $characters = self::getCharacters();
        $charactersLength = strlen($characters);
        for ($i = 0; $i < self::SALT_LENGTH; $i++) {
            $salt .= $characters[rand(0, $charactersLength - 1)];
        }
        return $salt;
    }

    public static function generateHash($salt, $password)
    {
        return sha1($salt . $password);
    }
}
