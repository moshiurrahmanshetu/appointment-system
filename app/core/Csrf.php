<?php

namespace App\Core;

class Csrf
{
    private static $tokenName = 'csrf_token';
    
    public static function generate()
    {
        if (!Session::has(self::$tokenName)) {
            Session::set(self::$tokenName, bin2hex(random_bytes(32)));
        }
        
        return Session::get(self::$tokenName);
    }
    
    public static function validate($token)
    {
        return Session::get(self::$tokenName) === $token;
    }
    
    public static function field()
    {
        $token = self::generate();
        return '<input type="hidden" name="' . self::$tokenName . '" value="' . $token . '">';
    }
    
    public static function getToken()
    {
        return self::generate();
    }
    
    public static function checkRequest()
    {
        $requestToken = $_POST[self::$tokenName] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        
        if ($requestToken === null) {
            return false;
        }
        
        return self::validate($requestToken);
    }
    
    public static function regenerate()
    {
        Session::set(self::$tokenName, bin2hex(random_bytes(32)));
    }
}
