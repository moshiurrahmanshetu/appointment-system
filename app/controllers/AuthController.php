<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Session;
use App\Models\User;

class AuthController extends Controller
{
    private $userModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }
    
    public function showLoginForm()
    {
        $data = [
            'title' => 'Login - ' . config('name'),
            'csrf_token' => Csrf::getToken()
        ];
        
        $this->view('auth/login', $data);
    }
    
    public function login()
    {
        // Validate CSRF token
        if (!Csrf::checkRequest()) {
            Session::setFlash('error', 'Invalid request. Please try again.');
            back();
        }
        
        $userId = $this->input('user_id');
        $password = $this->input('password');
        $remember = $this->input('remember') === 'on';
        
        // Validate input
        $errors = $this->validate([
            'user_id' => 'required',
            'password' => 'required'
        ]);
        
        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            set_old($this->only(['user_id']));
            back();
        }
        
        // Attempt authentication
        $user = $this->userModel->authenticate($userId, $password);
        
        if (!$user) {
            Session::setFlash('error', 'Invalid credentials or account is inactive.');
            set_old($this->only(['user_id']));
            back();
        }
        
        // Login the user
        $this->userModel->login($user, $remember);
        
        // Regenerate CSRF token
        Csrf::regenerate();
        
        Session::setFlash('success', 'Welcome back, ' . $user['full_name'] . '!');
        redirect('/dashboard');
    }
    
    public function logout()
    {
        $this->userModel->logout();
        Session::setFlash('success', 'You have been logged out successfully.');
        redirect('/login');
    }
}
