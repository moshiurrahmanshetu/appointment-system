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
        'last_login_ip'
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
        Session::set('user', [
            'id' => $user['id'],
            'user_id' => $user['user_id'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'role_id' => $user['role_id'],
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
}
