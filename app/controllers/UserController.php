<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Session;
use App\Models\AuditLog;
use App\Models\User;


class UserController extends Controller
{
    private $userModel;
    private $auditModel;
    protected $db;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->auditModel = new AuditLog();
        $this->db = \App\Core\Database::getInstance();
    }
    
    public function index($id = null)
    {
        // If ID is provided, redirect to show method
        if ($id) {
            return $this->show($id);
        }
        
        // Check permission
        if (!can('users.view')) {
            abort(403, 'You do not have permission to view users.');
        }
        
        $search = $this->input('search', '');
        $filters = [
            'role_id' => $this->input('role_id'),
            'status' => $this->input('status')
        ];
        
        if (!empty($search) || !empty(array_filter($filters))) {
            $users = $this->userModel->searchUsers($search, $filters);
        } else {
            $users = $this->userModel->getAllUsers();
        }
        
        $roles = $this->db->fetchAll("SELECT * FROM roles ORDER BY name ASC");
        
        $data = [
            'title' => 'Users - ' . config('name'),
            'users' => $users,
            'roles' => $roles,
            'search' => $search,
            'filters' => $filters,
            'csrf_token' => Csrf::getToken()
        ];
        
        $this->view('users/index', $data);
    }
    
    public function create()
    {
        // Check permission
        if (!can('users.create')) {
            abort(403, 'You do not have permission to create users.');
        }
        
        $roles = $this->db->fetchAll("SELECT * FROM roles ORDER BY name ASC");
        
        $data = [
            'title' => 'Create User - ' . config('name'),
            'roles' => $roles,
            'csrf_token' => Csrf::getToken()
        ];
        
        $this->view('users/create', $data);
    }
    
    public function store()
    {
        // Check permission
        if (!can('users.create')) {
            abort(403, 'User does not have permission to create users.');
        }
        
        // Validate CSRF token
        if (!Csrf::checkRequest()) {
            Session::setFlash('error', 'Invalid request. Please try again.');
            back();
        }
        
        $currentUser = Session::get('user');
        
        // Validate input
        $errors = $this->validate([
            'full_name' => 'required',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|confirmed',
            'role_id' => 'required'
        ]);
        
        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            set_old($this->only(['full_name', 'email', 'phone', 'address', 'username', 'gender', 'role_id', 'status']));
            back();
        }
        
        // Check email uniqueness
        $email = $this->input('email');
        if ($email && !$this->userModel->checkEmailAvailability($email)) {
            Session::setFlash('error', 'Email is already in use.');
            set_old($this->only(['full_name', 'email', 'phone', 'address', 'username', 'gender', 'role_id', 'status']));
            back();
        }
        
        // Check phone uniqueness
        $phone = $this->input('phone');
        if ($phone && !$this->userModel->checkPhoneAvailability($phone)) {
            Session::setFlash('error', 'Phone number is already in use.');
            set_old($this->only(['full_name', 'email', 'phone', 'address', 'username', 'gender', 'role_id', 'status']));
            back();
        }
        
        // Check username uniqueness
        $username = $this->input('username');
        if ($username && !$this->userModel->checkUsernameAvailability($username)) {
            Session::setFlash('error', 'Username is already in use.');
            set_old($this->only(['full_name', 'email', 'phone', 'address', 'username', 'gender', 'role_id', 'status']));
            back();
        }
        
        // Generate user ID
        $userId = $this->userModel->generateUserId();
        
        // Hash password
        $password = password_hash($this->input('password'), PASSWORD_DEFAULT);
        
        // Handle avatar upload
        $avatarPath = null;
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $avatarPath = $this->uploadAvatar($_FILES['avatar']);
        }
        
        // Create user
        $userData = [
            'user_id' => $userId,
            'password' => $password,
            'role_id' => $this->input('role_id'),
            'full_name' => $this->input('full_name'),
            'email' => $email,
            'phone' => $phone,
            'address' => $this->input('address'),
            'username' => $username,
            'gender' => $this->input('gender'),
            'avatar' => $avatarPath,
            'status' => $this->input('status', 'active'),
            'created_by' => $currentUser['id']
        ];
        
        $userIdDb = $this->userModel->create($userData);
        
        // Log audit
        $this->auditModel->log(
            $currentUser['id'],
            'user_created',
            'User',
            $userIdDb,
            null,
            $userData
        );
        
        Session::setFlash('success', 'User created successfully.');
        redirect('/users');
    }
    
    public function show($id)
    {
        // Check permission
        if (!can('users.view')) {
            abort(403, 'You do not have permission to view users.');
        }
        
        $user = $this->userModel->find($id);
        if (!$user || $user['deleted_at']) {
            abort(404, 'User not found.');
        }
        
        $role = $this->userModel->getRole($id);
        $createdBy = $user['created_by'] ? $this->userModel->find($user['created_by']) : null;
        
        $data = [
            'title' => 'User Profile - ' . config('name'),
            'user' => $user,
            'role' => $role,
            'created_by' => $createdBy,
            'csrf_token' => Csrf::getToken()
        ];
        
        $this->view('users/show', $data);
    }
    
    public function edit($id)
    {
        // Check permission
        if (!can('users.edit')) {
            abort(403, 'You do not have permission to edit users.');
        }
        
        $user = $this->userModel->find($id);
        if (!$user || $user['deleted_at']) {
            abort(404, 'User not found.');
        }
        
        $roles = $this->db->fetchAll("SELECT * FROM roles ORDER BY name ASC");
        
        $data = [
            'title' => 'Edit User - ' . config('name'),
            'user' => $user,
            'roles' => $roles,
            'csrf_token' => Csrf::getToken()
        ];
        
        $this->view('users/edit', $data);
    }
    
    public function update($id)
    {
        // Check permission
        if (!can('users.edit')) {
            abort(403, 'User does not have permission to edit users.');
        }
        
        // Validate CSRF token
        if (!Csrf::checkRequest()) {
            Session::setFlash('error', 'Invalid request. Please try again.');
            back();
        }
        
        $currentUser = Session::get('user');
        
        $user = $this->userModel->find($id);
        if (!$user || $user['deleted_at']) {
            abort(404, 'user not found.');
        }
        
        // Validate input
        $errors = $this->validate([
            'full_name' => 'required',
            'role_id' => 'required'
        ]);
        
        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            set_old($this->only(['full_name', 'email', 'phone', 'address', 'username', 'gender', 'role_id', 'status']));
            back();
        }
        
        // Check email uniqueness
        $email = $this->input('email');
        if ($email && !$this->userModel->checkEmailAvailability($email, $id)) {
            Session::setFlash('error', 'Email is already in use by another user.');
            set_old($this->only(['full_name', 'email', 'phone', 'address', 'username', 'gender', 'role_id', 'status']));
            back();
        }
        
        // Check phone uniqueness
        $phone = $this->input('phone');
        if ($phone && !$this->userModel->checkPhoneAvailability($phone, $id)) {
            Session::setFlash('error', 'Phone number is already in use by another user.');
            set_old($this->only(['full_name', 'email', 'phone', 'address', 'username', 'gender', 'role_id', 'status']));
            back();
        }
        
        // Check username uniqueness
        $username = $this->input('username');
        if ($username && !$this->userModel->checkUsernameAvailability($username, $id)) {
            Session::setFlash('error', 'Username is already in use by another user.');
            set_old($this->only(['full_name', 'email', 'phone', 'address', 'username', 'gender', 'role_id', 'status']));
            back();
        }
        
        // Handle avatar upload
        $avatarPath = $user['avatar'];
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $newAvatarPath = $this->uploadAvatar($_FILES['avatar']);
            if ($newAvatarPath) {
                // Delete old avatar if exists
                if ($avatarPath) {
                    $oldAvatarPath = __DIR__ . '/../../public/assets/' . $avatarPath;
                    if (file_exists($oldAvatarPath)) {
                        unlink($oldAvatarPath);
                    }
                }
                $avatarPath = $newAvatarPath;
            }
        }
        
        // Store old values for audit
        $oldValues = [
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'address' => $user['address'],
            'username' => $user['username'],
            'gender' => $user['gender'],
            'role_id' => $user['role_id'],
            'status' => $user['status'],
            'avatar' => $user['avatar']
        ];
        
        // Update user
        $userData = [
            'full_name' => $this->input('full_name'),
            'email' => $email,
            'phone' => $phone,
            'address' => $this->input('address'),
            'username' => $username,
            'gender' => $this->input('gender'),
            'role_id' => $this->input('role_id'),
            'status' => $this->input('status', $user['status']),
            'avatar' => $avatarPath
        ];
        
        $this->userModel->update($id, $userData);
        
        // Log audit
        $this->auditModel->log(
            $currentUser['id'],
            'user_updated',
            'User',
            $id,
            $oldValues,
            $userData
        );
        
        Session::setFlash('success', 'User updated successfully.');
        redirect('/users');
    }
    
    public function delete($id)
    {
        // Check permission
        if (!can('users.delete')) {
            abort(403, 'You do not have permission to delete users.');
        }
        
        $user = $this->userModel->find($id);
        if (!$user || $user['deleted_at']) {
            abort(404, 'User not found.');
        }
        
        // Prevent deleting own account
        $currentUser = Session::get('user');
        if ($id == $currentUser['id']) {
            Session::setFlash('error', 'You cannot delete your own account.');
            back();
        }
        
        // Soft delete
        $this->userModel->softDelete($id);
        
        // Log audit
        $this->auditModel->log(
            $currentUser['id'],
            'user_deleted',
            'User',
            $id,
            $user,
            ['deleted_at' => date('Y-m-d H:i:s')]
        );
        
        Session::setFlash('success', 'User deleted successfully.');
        redirect('/users');
    }
    
    public function restore($id)
    {
        // Check permission
        if (!can('users.restore')) {
            abort(403, 'You do not have permission to restore users.');
        }
        
        $user = $this->userModel->find($id);
        if (!$user || !$user['deleted_at']) {
            abort(404, 'User not found or not deleted.');
        }
        
        // Restore user
        $this->userModel->restore($id);
        
        // Log audit
        $this->auditModel->log(
            Session::get('user')['id'],
            'user_restored',
            'User',
            $id,
            ['deleted_at' => $user['deleted_at']],
            ['deleted_at' => null]
        );
        
        Session::setFlash('success', 'User restored successfully.');
        redirect('/users');
    }
    
    public function updateStatus($id)
    {
        // Check permission
        if (!can('users.status')) {
            abort(403, 'You do not have permission to change user status.');
        }
        
        $user = $this->userModel->find($id);
        if (!$user || $user['deleted_at']) {
            abort(404, 'User not found.');
        }
        
        $status = $this->input('status');
        if (!in_array($status, ['active', 'inactive', 'blocked'])) {
            Session::setFlash('error', 'Invalid status.');
            back();
        }
        
        // Prevent blocking own account
        $currentUser = Session::get('user');
        if ($id == $currentUser['id'] && $status === 'blocked') {
            Session::setFlash('error', 'You cannot block your own account.');
            back();
        }
        
        // Update status
        $this->userModel->updateStatus($id, $status);
        
        // Log audit
        $this->auditModel->log(
            $currentUser['id'],
            'user_status_changed',
            'User',
            $id,
            ['status' => $user['status']],
            ['status' => $status]
        );
        
        Session::setFlash('success', 'User status updated successfully.');
        redirect('/users');
    }
    
    private function uploadAvatar($file)
    {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        
        // Validate file type
        if (!in_array($file['type'], $allowedTypes)) {
            Session::setFlash('error', 'Invalid file type. Only JPG, JPEG, and PNG files are allowed.');
            return null;
        }
        
        // Validate file size
        if ($file['size'] > $maxSize) {
            Session::setFlash('error', 'File size exceeds 2MB limit.');
            return null;
        }
        
        // Create avatars directory if it doesn't exist
        $uploadDir = __DIR__ . '/../../public/assets/images/avatars/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        
        $uploadPath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return 'images/avatars/' . $filename;
        }
        
        return null;
    }
}