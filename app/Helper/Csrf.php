<?php
namespace Memtext\Helper;

class Csrf
{
    public static function init()
    {
        if (!isset($_COOKIE['csrf_token'])) {
            $token = HashGenerator::generateSalt();
        } else {
            $token = $_COOKIE['csrf_token'];
        }
        setcookie('csrf_token', $token, time() + 24*60*60, '/');
        return $token;
    }
}
