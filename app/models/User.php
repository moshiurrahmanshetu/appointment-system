<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Session;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'password',
        'role_id',
        'full_name',
        'email',
        'phone',
        'address',
        'avatar',
        'is_active',
        'remember_token',
        'last_login_at',
        'last_login_ip',
        'username',
        'gender',
        'status',
        'created_by',
        'deleted_at'
    ];
    
    public function findByUserId($userId)
    {
        return $this->findBy('user_id', $userId);
    }
    
    public function findByEmail($email)
    {
        return $this->findBy('email', $email);
    }
    
    public function authenticate($userId, $password)
    {
        $user = $this->findByUserId($userId);
        
        if (!$user) {
            return false;
        }
        
        if (!$user['is_active']) {
            return false;
        }
        
        if (!password_verify($password, $user['password'])) {
            return false;
        }
        
        return $user;
    }
    
    public function login($user, $remember = false)
    {
        // Load user role and permissions
        $role = $this->getRole($user['id']);
        $permissions = $this->getUserPermissions($user['id']);
        
        Session::set('user', [
            'id' => $user['id'],
            'user_id' => $user['user_id'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'role_id' => $user['role_id'],
            'role' => $role,
            'permissions' => $permissions,
            'avatar' => $user['avatar']
        ]);
        
        Session::regenerate();
        
        // Update last login
        $this->update($user['id'], [
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ]);
        
        if ($remember) {
            $this->setRememberToken($user['id']);
        }
    }
    
    public function logout()
    {
        $user = Session::get('user');
        
        if ($user) {
            $this->clearRememberToken($user['id']);
        }
        
        Session::destroy();
    }
    
    private function setRememberToken($userId)
    {
        $token = bin2hex(random_bytes(32));
        
        $this->update($userId, [
            'remember_token' => $token
        ]);
        
        // Set cookie
        $cookieName = config('session.name') . '_remember';
        $cookieValue = $userId . '|' . $token;
        $lifetime = time() + (30 * 24 * 60 * 60); // 30 days
        
        setcookie(
            $cookieName,
            $cookieValue,
            $lifetime,
            config('session.path'),
            config('session.domain'),
            config('session.secure'),
            config('session.httponly')
        );
    }
    
    private function clearRememberToken($userId)
    {
        $this->update($userId, [
            'remember_token' => null
        ]);
        
        $cookieName = config('session.name') . '_remember';
        
        if (isset($_COOKIE[$cookieName])) {
            setcookie(
                $cookieName,
                '',
                time() - 3600,
                config('session.path'),
                config('session.domain'),
                config('session.secure'),
                config('session.httponly')
            );
        }
    }
    
    public function getByRememberToken($cookie)
    {
        if (!$cookie) {
            return null;
        }
        
        list($userId, $token) = explode('|', $cookie);
        
        $user = $this->find($userId);
        
        if (!$user || !$user['remember_token'] || $user['remember_token'] !== $token) {
            return null;
        }
        
        return $user;
    }
    
    public function updatePassword($userId, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        return $this->update($userId, [
            'password' => $hashedPassword
        ]);
    }
    
    public function verifyPassword($userId, $password)
    {
        $user = $this->find($userId);
        
        if (!$user) {
            return false;
        }
        
        return password_verify($password, $user['password']);
    }
    
    public function getRole($userId)
    {
        $sql = "SELECT r.* FROM roles r 
                INNER JOIN users u ON u.role_id = r.id 
                WHERE u.id = :user_id";
        
        return $this->db->fetch($sql, ['user_id' => $userId]);
    }
    
    public function getUserPermissions($userId)
    {
        $sql = "SELECT p.* FROM permissions p 
                INNER JOIN role_permissions rp ON rp.permission_id = p.id
                INNER JOIN users u ON u.role_id = rp.role_id
                WHERE u.id = :user_id";
        
        return $this->db->fetchAll($sql, ['user_id' => $userId]);
    }
    
    public function hasPermission($userId, $permission)
    {
        $permissions = $this->getUserPermissions($userId);
        $permissionSlugs = array_column($permissions, 'slug');
        
        if (is_array($permission)) {
            return !empty(array_intersect($permission, $permissionSlugs));
        }
        
        return in_array($permission, $permissionSlugs);
    }
    
    public function generateUserId()
    {
        do {
            $userId = 'USR' . strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6)) . substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 2);
        } while ($this->exists('user_id', $userId));
        
        return $userId;
    }
    
    public function findByUsername($username)
    {
        return $this->findBy('username', $username);
    }
    
    public function getAllUsers($onlyActive = true, $includeDeleted = false)
    {
        $sql = "SELECT u.*, r.name as role_name, r.slug as role_slug 
                FROM users u 
                INNER JOIN roles r ON u.role_id = r.id";
        
        $params = [];
        
        if ($onlyActive) {
            $sql .= " WHERE u.status = 'active'";
        }
        
        if (!$includeDeleted) {
            if ($onlyActive) {
                $sql .= " AND u.deleted_at IS NULL";
            } else {
                $sql .= " WHERE u.deleted_at IS NULL";
            }
        }
        
        $sql .= " ORDER BY u.created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getUsersByRole($roleId, $onlyActive = true)
    {
        $sql = "SELECT u.*, r.name as role_name, r.slug as role_slug 
                FROM users u 
                INNER JOIN roles r ON u.role_id = r.id 
                WHERE u.role_id = :role_id";
        
        $params = ['role_id' => $roleId];
        
        if ($onlyActive) {
            $sql .= " AND u.status = 'active' AND u.deleted_at IS NULL";
        }
        
        $sql .= " ORDER BY u.created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getUsersByStatus($status)
    {
        $sql = "SELECT u.*, r.name as role_name, r.slug as role_slug 
                FROM users u 
                INNER JOIN roles r ON u.role_id = r.id 
                WHERE u.status = :status AND u.deleted_at IS NULL
                ORDER BY u.created_at DESC";
        
        return $this->db->fetchAll($sql, ['status' => $status]);
    }
    
    public function searchUsers($searchTerm, $filters = [])
    {
        $sql = "SELECT u.*, r.name as role_name, r.slug as role_slug 
                FROM users u 
                INNER JOIN roles r ON u.role_id = r.id 
                WHERE u.deleted_at IS NULL";
        
        $params = [];
        
        // Search condition
        if (!empty($searchTerm)) {
            $sql .= " AND (u.user_id LIKE :search OR u.full_name LIKE :search OR u.email LIKE :search OR u.phone LIKE :search)";
            $params['search'] = '%' . $searchTerm . '%';
        }
        
        // Filter by role
        if (!empty($filters['role_id'])) {
            $sql .= " AND u.role_id = :role_id";
            $params['role_id'] = $filters['role_id'];
        }
        
        // Filter by status
        if (!empty($filters['status'])) {
            $sql .= " AND u.status = :status";
            $params['status'] = $filters['status'];
        }
        
        $sql .= " ORDER BY u.created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function softDelete($userId)
    {
        return $this->update($userId, [
            'deleted_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    public function restore($userId)
    {
        return $this->update($userId, [
            'deleted_at' => null
        ]);
    }
    
    public function updateStatus($userId, $status)
    {
        return $this->update($userId, [
            'status' => $status
        ]);
    }
    
    public function updateAvatar($userId, $avatarPath)
    {
        return $this->update($userId, [
            'avatar' => $avatarPath
        ]);
    }
    
    public function checkUserIdAvailability($userId, $excludeId = null)
    {
        return !$this->exists('user_id', $userId, $excludeId);
    }
    
    public function checkEmailAvailability($email, $excludeId = null)
    {
        return !$this->exists('email', $email, $excludeId);
    }
    
    public function checkPhoneAvailability($phone, $excludeId = null)
    {
        return !$this->exists('phone', $phone, $excludeId);
    }
    
    public function checkUsernameAvailability($username, $excludeId = null)
    {
        return !$this->exists('username', $username, $excludeId);
    }
}
