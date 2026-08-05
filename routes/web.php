<?php

use App\Core\Router;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

// Load helpers
require_once __DIR__ . '/../app/helpers/functions.php';

// Initialize application
$app = \App\Core\Application::getInstance();

// Initialize router
$router = new Router();

// Root route - handles authentication-based redirect
$router->get('/', 'HomeController@index', []);

// Guest routes (no authentication required)
$router->get('/login', 'AuthController@showLoginForm', [GuestMiddleware::class]);
$router->post('/login', 'AuthController@login', [GuestMiddleware::class]);

// Authenticated routes (authentication required)
$router->get('/logout', 'AuthController@logout', [AuthMiddleware::class]);
$router->post('/logout', 'AuthController@logout', [AuthMiddleware::class]);
$router->get('/dashboard', 'DashboardController@index', [AuthMiddleware::class]);

// Profile routes
$router->get('/profile', 'ProfileController@show', [AuthMiddleware::class]);
$router->get('/profile/edit', 'ProfileController@edit', [AuthMiddleware::class]);
$router->post('/profile/edit', 'ProfileController@update', [AuthMiddleware::class]);
$router->get('/profile/change-password', 'ProfileController@showChangePassword', [AuthMiddleware::class]);
$router->post('/profile/change-password', 'ProfileController@changePassword', [AuthMiddleware::class]);

// Dispatch the router
$router->dispatch();
