<?php

// Detect base URL dynamically
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$baseUrl = $protocol . '://' . $host . $scriptName;

return [
    'name' => 'Appointment Queue System',
    'version' => '1.0.0',
    'url' => getenv('APP_URL') ?: $baseUrl,
    'timezone' => 'UTC',
    'locale' => 'en',
    
    'session' => [
        'name' => 'appointment_session',
        'lifetime' => 7200, // 2 hours
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Strict'
    ],
    
    'security' => [
        'csrf_token_name' => 'csrf_token',
        'csrf_header_name' => 'X-CSRF-TOKEN',
    ]
];