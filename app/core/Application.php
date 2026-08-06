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
        
        // Dynamically detect URL if not set
        if ($key === 'url' && empty($this->config['url'])) {
            $this->config['url'] = $this->detectBaseUrl();
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
    
    private function detectBaseUrl()
    {
        // For CLI or when running tests, return a default
        if (php_sapi_name() === 'cli') {
            return 'http://localhost:8080';
        }
        
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $port = $_SERVER['SERVER_PORT'] ?? '80';
        
        // Include port if not standard HTTP/HTTPS ports
        if (($protocol === 'http' && $port != '80') || ($protocol === 'https' && $port != '443')) {
            $host .= ':' . $port;
        }
        
        // The script is in public/index.php, so we need the path up to public
        // SCRIPT_NAME will be something like /appointment-system/public/index.php
        // We want /appointment-system/public
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        
        // Fix for Windows paths - ensure web path format
        $scriptName = str_replace('\\', '/', $scriptName);
        
        // Remove duplicate slashes
        $scriptName = preg_replace('/\/+/', '/', $scriptName);
        
        // Ensure it doesn't end with a slash
        $scriptName = rtrim($scriptName, '/');
        
        // If scriptName is empty or just '/', we're at root
        if (empty($scriptName) || $scriptName === '.') {
            $scriptName = '';
        }
        
        return $protocol . '://' . $host . $scriptName;
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
