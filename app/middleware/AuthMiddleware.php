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
            redirect('/login');
            return false;
        }
        
        return true;
    }
}
