<?php

namespace App\Middleware;

use App\Core\Application;
use App\Core\Session;

class GuestMiddleware
{
    public function handle()
    {
        if (Session::has('user')) {
            redirect('/dashboard');
            return false;
        }
        
        return true;
    }
}
