<?php

return [
    'name' => 'Appointment Queue System',
    'version' => '1.0.0',
    'url' => getenv('APP_URL') ?: null, // Will be detected dynamically
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