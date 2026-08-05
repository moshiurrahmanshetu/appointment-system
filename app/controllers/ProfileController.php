<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Session;
use App\Models\User;

class ProfileController extends Controller
{
    private $userModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }
    
    public function show()
    {
        // Check permission
        if (!can('profile.view')) {
            abort(403, 'You do not have permission to view profiles.');
        }
        
        $user = Session::get('user');
        $fullUser = $this->userModel->find($user['id']);
        $role = $this->userModel->getRole($user['id']);
        
        $data = [
            'title' => 'My Profile - ' . config('name'),
            'user' => $fullUser,
            'role' => $role,
            'csrf_token' => Csrf::getToken()
        ];
        
        $this->view('profile/show', $data);
    }
    
    public function edit()
    {
        // Check permission
        if (!can('profile.edit')) {
            abort(403, 'You do not have permission to edit profiles.');
        }
        
        $user = Session::get('user');
        $fullUser = $this->userModel->find($user['id']);
        
        $data = [
            'title' => 'Edit Profile - ' . config('name'),
            'user' => $fullUser,
            'csrf_token' => Csrf::getToken()
        ];
        
        $this->view('profile/edit', $data);
    }
    
    public function update()
    {
        // Check permission
        if (!can('profile.edit')) {
            abort(403, 'You do not have permission to edit profiles.');
        }
        
        // Validate CSRF token
        if (!Csrf::checkRequest()) {
            Session::setFlash('error', 'Invalid request. Please try again.');
            back();
        }
        
        $user = Session::get('user');
        
        // Validate input
        $errors = $this->validate([
            'full_name' => 'required',
            'email' => 'email'
        ]);
        
        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            set_old($this->only(['full_name', 'email', 'phone', 'address']));
            back();
        }
        
        // Check if email is unique
        $email = $this->input('email');
        if ($email && $this->userModel->exists('email', $email, $user['id'])) {
            Session::setFlash('error', 'Email is already in use by another user.');
            set_old($this->only(['full_name', 'email', 'phone', 'address']));
            back();
        }
        
        // Update user
        $updateData = [
            'full_name' => $this->input('full_name'),
            'email' => $email,
            'phone' => $this->input('phone'),
            'address' => $this->input('address')
        ];
        
        $this->userModel->update($user['id'], $updateData);
        
        // Update session
        Session::set('user', array_merge(Session::get('user'), [
            'full_name' => $updateData['full_name'],
            'email' => $updateData['email']
        ]));
        
        Session::setFlash('success', 'Profile updated successfully.');
        redirect('/profile');
    }
    
    public function showChangePassword()
    {
        // Check permission
        if (!can('profile.change_password')) {
            abort(403, 'You do not have permission to change passwords.');
        }
        
        $data = [
            'title' => 'Change Password - ' . config('name'),
            'csrf_token' => Csrf::getToken()
        ];
        
        $this->view('profile/change-password', $data);
    }
    
    public function changePassword()
    {
        // Check permission
        if (!can('profile.change_password')) {
            abort(403, 'You do not have permission to change passwords.');
        }
        
        // Validate CSRF token
        if (!Csrf::checkRequest()) {
            Session::setFlash('error', 'Invalid request. Please try again.');
            back();
        }
        
        $user = Session::get('user');
        
        // Validate input
        $errors = $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6',
            'new_password_confirmation' => 'required|confirmed'
        ]);
        
        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            back();
        }
        
        // Verify current password
        $currentPassword = $this->input('current_password');
        if (!$this->userModel->verifyPassword($user['id'], $currentPassword)) {
            Session::setFlash('error', 'Current password is incorrect.');
            back();
        }
        
        // Update password
        $newPassword = $this->input('new_password');
        $this->userModel->updatePassword($user['id'], $newPassword);
        
        Session::setFlash('success', 'Password changed successfully.');
        redirect('/profile');
    }
}
