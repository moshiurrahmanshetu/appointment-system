<?php

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Load environment variables if .env file exists
if (file_exists(BASE_PATH . '/.env')) {
    $lines = file(BASE_PATH . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone
date_default_timezone_set('UTC');

// Autoloader for classes with namespaces
spl_autoload_register(function ($class) {
    // Convert namespace to file path
    $class = str_replace('\\', '/', $class);
    $file = BASE_PATH . '/' . $class . '.php';
    
    // Check if file exists
    if (file_exists($file)) {
        require_once $file;
    }
});

// Load the routes
require_once BASE_PATH . '/routes/web.php';
