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

// Patient Management routes with permission checks
$router->get('/patients', 'PatientController@index', [AuthMiddleware::class, new PermissionMiddleware('patients.view')]);
$router->get('/patients/create', 'PatientController@create', [AuthMiddleware::class, new PermissionMiddleware('patients.create')]);
$router->post('/patients', 'PatientController@store', [AuthMiddleware::class, new PermissionMiddleware('patients.create')]);
$router->get('/patients/show/{id}', 'PatientController@show', [AuthMiddleware::class, new PermissionMiddleware('patients.view')]);
$router->get('/patients/edit/{id}', 'PatientController@edit', [AuthMiddleware::class, new PermissionMiddleware('patients.edit')]);
$router->post('/patients/update/{id}', 'PatientController@update', [AuthMiddleware::class, new PermissionMiddleware('patients.edit')]);
$router->get('/patients/delete/{id}', 'PatientController@delete', [AuthMiddleware::class, new PermissionMiddleware('patients.delete')]);
$router->get('/patients/restore/{id}', 'PatientController@restore', [AuthMiddleware::class, new PermissionMiddleware('patients.restore')]);
$router->get('/patients/status/{id}', 'PatientController@updateStatus', [AuthMiddleware::class, new PermissionMiddleware('patients.status')]);
$router->get('/patients/slip/{id}', 'PatientController@slip', [AuthMiddleware::class, new PermissionMiddleware('patients.view')]);

// Appointment Management routes with permission checks
$router->get('/appointments', 'AppointmentController@index', [AuthMiddleware::class, new PermissionMiddleware('appointments.view')]);
$router->get('/appointments/create', 'AppointmentController@create', [AuthMiddleware::class, new PermissionMiddleware('appointments.create')]);
$router->post('/appointments', 'AppointmentController@store', [AuthMiddleware::class, new PermissionMiddleware('appointments.create')]);
$router->get('/appointments/show/{id}', 'AppointmentController@show', [AuthMiddleware::class, new PermissionMiddleware('appointments.view')]);
$router->get('/appointments/edit/{id}', 'AppointmentController@edit', [AuthMiddleware::class, new PermissionMiddleware('appointments.edit')]);
$router->post('/appointments/update/{id}', 'AppointmentController@update', [AuthMiddleware::class, new PermissionMiddleware('appointments.edit')]);
$router->get('/appointments/delete/{id}', 'AppointmentController@delete', [AuthMiddleware::class, new PermissionMiddleware('appointments.delete')]);
$router->get('/appointments/restore/{id}', 'AppointmentController@restore', [AuthMiddleware::class, new PermissionMiddleware('appointments.restore')]);
$router->get('/appointments/status/{id}', 'AppointmentController@updateStatus', [AuthMiddleware::class, new PermissionMiddleware('appointments.edit')]);

// Queue Management routes with permission checks
$router->get('/queue', 'QueueController@index', [AuthMiddleware::class, new PermissionMiddleware('queue.view')]);
$router->post('/queue/call-next', 'QueueController@callNext', [AuthMiddleware::class, new PermissionMiddleware('queue.call')]);
$router->get('/queue/call/{id}', 'QueueController@callSpecific', [AuthMiddleware::class, new PermissionMiddleware('queue.call')]);
$router->get('/queue/start/{id}', 'QueueController@startConsultation', [AuthMiddleware::class, new PermissionMiddleware('queue.manage')]);
$router->get('/queue/complete/{id}', 'QueueController@completeQueue', [AuthMiddleware::class, new PermissionMiddleware('queue.complete')]);
$router->get('/queue/skip/{id}', 'QueueController@skipQueue', [AuthMiddleware::class, new PermissionMiddleware('queue.skip')]);
$router->get('/queue/cancel/{id}', 'QueueController@cancelQueue', [AuthMiddleware::class, new PermissionMiddleware('queue.cancel')]);
$router->get('/queue/show/{id}', 'QueueController@show', [AuthMiddleware::class, new PermissionMiddleware('queue.view')]);

// Dispatch the router
$router->dispatch();
