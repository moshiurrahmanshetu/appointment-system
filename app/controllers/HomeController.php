<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;

class HomeController extends Controller
{
    public function index()
    {
        echo "<div style='background: #ffffcc; padding: 20px; margin: 20px; border: 1px solid #ccc;'>";
        echo "<h3>HomeController Debug</h3>";
        echo "<strong>Session has user:</strong> " . (Session::has('user') ? 'YES' : 'NO') . "<br>";
        
        if (Session::has('user')) {
            $user = Session::get('user');
            echo "<strong>User data:</strong> <pre>" . print_r($user, true) . "</pre>";
            echo "<strong>About to redirect to /dashboard</strong><br>";
            echo "</div>";
            redirect('/dashboard');
        }
        
        echo "<strong>About to redirect to /login</strong><br>";
        echo "</div>";
        redirect('/login');
    }
}
