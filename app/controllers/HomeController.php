<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;

class HomeController extends Controller
{
    public function index()
    {
        if (Session::has('user')) {
            redirect('/dashboard');
        }
        
        redirect('/login');
    }
}
