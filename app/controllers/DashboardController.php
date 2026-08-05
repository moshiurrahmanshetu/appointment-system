<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Session;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Session::get('user');
        
        $data = [
            'title' => 'Dashboard - ' . config('name'),
            'user' => $user,
            'csrf_token' => Csrf::getToken()
        ];
        
        $this->view('dashboard/index', $data);
    }
}
