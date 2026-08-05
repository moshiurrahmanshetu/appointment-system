<?php

use App\Core\Router;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\PermissionMiddleware;

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

// Dashboard route with permission check
$router->get('/dashboard', 'DashboardController@index', [AuthMiddleware::class, new PermissionMiddleware('dashboard.view')]);

// Profile routes with permission checks
$router->get('/profile', 'ProfileController@show', [AuthMiddleware::class, new PermissionMiddleware('profile.view')]);
$router->get('/profile/edit', 'ProfileController@edit', [AuthMiddleware::class, new PermissionMiddleware('profile.edit')]);
$router->post('/profile/edit', 'ProfileController@update', [AuthMiddleware::class, new PermissionMiddleware('profile.edit')]);
$router->get('/profile/change-password', 'ProfileController@showChangePassword', [AuthMiddleware::class, new PermissionMiddleware('profile.change_password')]);
$router->post('/profile/change-password', 'ProfileController@changePassword', [AuthMiddleware::class, new PermissionMiddleware('profile.change_password')]);

// User Management routes with permission checks
$router->get('/users', 'UserController@index', [AuthMiddleware::class, new PermissionMiddleware('users.view')]);
$router->get('/users/create', 'UserController@create', [AuthMiddleware::class, new PermissionMiddleware('users.create')]);
$router->post('/users', 'UserController@store', [AuthMiddleware::class, new PermissionMiddleware('users.create')]);
$router->get('/users/show/{id}', 'UserController@show', [AuthMiddleware::class, new PermissionMiddleware('users.view')]);
$router->get('/users/edit/{id}', 'UserController@edit', [AuthMiddleware::class, new PermissionMiddleware('users.edit')]);
$router->post('/users/update/{id}', 'UserController@update', [AuthMiddleware::class, new PermissionMiddleware('users.edit')]);
$router->get('/users/delete/{id}', 'UserController@delete', [AuthMiddleware::class, new PermissionMiddleware('users.delete')]);
$router->get('/users/restore/{id}', 'UserController@restore', [AuthMiddleware::class, new PermissionMiddleware('users.restore')]);
$router->get('/users/status/{id}', 'UserController@updateStatus', [AuthMiddleware::class, new PermissionMiddleware('users.status')]);

// Dispatch the router
$router->dispatch();
