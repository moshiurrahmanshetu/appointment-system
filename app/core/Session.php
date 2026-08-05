<?php

namespace App\Core;

class Session
{
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    
    public static function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }
    
    public static function has($key)
    {
        return isset($_SESSION[$key]);
    }
    
    public static function remove($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    public static function clear()
    {
        $_SESSION = [];
    }
    
    public static function destroy()
    {
        self::clear();
        
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        
        session_destroy();
    }
    
    public static function regenerate()
    {
        session_regenerate_id(true);
    }
    
    public static function flash($key, $value = null)
    {
        if ($value === null) {
            $value = self::get($key);
            self::remove($key);
            return $value;
        }
        
        self::set($key, $value);
    }
    
    public static function setFlash($key, $value)
    {
        self::set('_flash_' . $key, $value);
    }
    
    public static function getFlash($key, $default = null)
    {
        $value = self::get('_flash_' . $key, $default);
        self::remove('_flash_' . $key);
        return $value;
    }
    
    public static function hasFlash($key)
    {
        return self::has('_flash_' . $key);
    }
}
