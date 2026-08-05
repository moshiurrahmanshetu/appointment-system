<?php

namespace App\Middleware;

use App\Core\Application;
use App\Core\Session;

class PermissionMiddleware
{
    private $permission;
    
    public function __construct($permission = null)
    {
        $this->permission = $permission;
    }
    
    public function handle()
    {
        $user = Session::get('user');
        
        if (!$user) {
            Session::setFlash('error', 'You must be logged in to access this page.');
            redirect('/login');
            return false;
        }
        
        // Check permission if specified
        if ($this->permission) {
            if (!$this->hasPermission($user['id'], $this->permission)) {
                Session::setFlash('error', 'You do not have permission to access this page.');
                redirect('/dashboard');
                return false;
            }
        }
        
        return true;
    }
    
    private function hasPermission($userId, $permission)
    {
        $db = \App\Core\Database::getInstance();
        
        // Support multiple permissions (array)
        if (is_array($permission)) {
            $placeholders = str_repeat('?,', count($permission) - 1) . '?';
            $sql = "SELECT COUNT(*) as count FROM permissions p 
                    INNER JOIN role_permissions rp ON rp.permission_id = p.id
                    INNER JOIN users u ON u.role_id = rp.role_id
                    WHERE u.id = ? AND p.slug IN ($placeholders)";
            $params = array_merge([$userId], $permission);
        } else {
            $sql = "SELECT COUNT(*) as count FROM permissions p 
                    INNER JOIN role_permissions rp ON rp.permission_id = p.id
                    INNER JOIN users u ON u.role_id = rp.role_id
                    WHERE u.id = ? AND p.slug = ?";
            $params = [$userId, $permission];
        }
        
        $result = $db->fetch($sql, $params);
        return $result['count'] > 0;
    }
}
