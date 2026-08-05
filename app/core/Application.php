<?php

namespace App\Core;

class Application
{
    private static $instance = null;
    private $config;
    
    private function __construct()
    {
        $this->config = require __DIR__ . '/../../config/app.php';
        $this->setTimezone();
        $this->startSession();
    }
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConfig($key = null)
    {
        if ($key === null) {
            return $this->config;
        }
        
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            $value = $this->config;
            
            foreach ($keys as $k) {
                if (isset($value[$k])) {
                    $value = $value[$k];
                } else {
                    return null;
                }
            }
            
            return $value;
        }
        
        return $this->config[$key] ?? null;
    }
    
    private function setTimezone()
    {
        date_default_timezone_set($this->config['timezone']);
    }
    
    private function startSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            $sessionConfig = $this->config['session'];
            
            session_name($sessionConfig['name']);
            session_set_cookie_params([
                'lifetime' => $sessionConfig['lifetime'],
                'path' => $sessionConfig['path'],
                'domain' => $sessionConfig['domain'],
                'secure' => $sessionConfig['secure'],
                'httponly' => $sessionConfig['httponly'],
                'samesite' => $sessionConfig['samesite']
            ]);
            
            session_start();
        }
    }
    
    public function getUrl()
    {
        return $this->config['url'];
    }
}
