<?php

namespace App\Middleware;

use App\Core\Application;
use App\Core\Session;

class AuthMiddleware
{
    public function handle()
    {
        if (!Session::has('user')) {
            Session::setFlash('error', 'You must be logged in to access this page.');
            $app = Application::getInstance();
            $appUrl = $app->getConfig('url');
            header('Location: ' . rtrim($appUrl, '/') . '/login');
            exit;
            return false;
        }
        
        return true;
    }
}
