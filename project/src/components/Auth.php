<?php
namespace thewulf7\friendloc\components;


/**
 * Class Auth
 *
 * @package thewulf7\friendloc\components
 */
class Auth
{
    /**
     * Generate salt
     *
     * @return mixed
     */
    public static function generateSalt(): string
    {
        $salt = '$2a$10$' . substr(str_replace('+', '.', base64_encode(pack('N4', mt_rand(), mt_rand(), mt_rand(), mt_rand()))), 0, 22) . '$';

        return $salt;
    }

    /**
     * Generate password with $length length
     *
     * @param int $length
     *
     * @return mixed
     */
    public static function generatePassword(int $length = 8): string
    {
        $pass     = [];
        $alphabet = 'abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789';
        for ($i = 0; $i <= $length; $i++)
        {
            $n        = mt_rand(0, strlen($alphabet) - 1);
            $pass[$i] = $alphabet[$n];
        }

        return implode('',$pass);
    }

    /**
     * Create password based on pass and salt
     *
     * @param $password
     * @param $salt
     *
     * @return mixed
     */
    public static function createPassword($password, $salt): string
    {
        return crypt($password, $salt);
    }

    /**
     * @param $hash
     */
    public static function setAuth($hash)
    {
        $time = new \DateTime('+1 month');

        setcookie('AUTH_KEY', $hash, $time->getTimestamp(), '/');
    }

    /**
     *
     */
    public static function logout()
    {
        unset($_COOKIE['AUTH_KEY']);
        setcookie('AUTH_KEY', null, 1, '/');
    }

    /**
     * @return bool
     */
    public static function getHash()
    {

        if(isset($_SERVER['AUTH_KEY']))
        {
            return $_SERVER['AUTH_KEY'];
        }

        if(isset($_COOKIE['AUTH_KEY']))
        {
            return $_COOKIE['AUTH_KEY'];
        }

        return false;
    }
}