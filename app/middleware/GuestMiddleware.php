<?php

namespace App\Middleware;

use App\Core\Application;
use App\Core\Session;

class GuestMiddleware
{
    public function handle()
    {
        if (Session::has('user')) {
            $app = Application::getInstance();
            $appUrl = $app->getConfig('url');
            header('Location: ' . rtrim($appUrl, '/') . '/dashboard');
            exit;
            return false;
        }
        
        return true;
    }
}
