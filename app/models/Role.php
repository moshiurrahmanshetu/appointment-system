<?php

namespace App\Models;

use App\Core\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'slug',
        'description'
    ];
    
    public function findBySlug($slug)
    {
        return $this->findBy('slug', $slug);
    }
    
    public function getPermissions($roleId)
    {
        $sql = "SELECT p.* FROM permissions p 
                INNER JOIN role_permissions rp ON rp.permission_id = p.id
                WHERE rp.role_id = :role_id";
        
        return $this->db->fetchAll($sql, ['role_id' => $roleId]);
    }
    
    public function assignPermission($roleId, $permissionId)
    {
        $sql = "INSERT INTO role_permissions (role_id, permission_id) 
                VALUES (:role_id, :permission_id)
                ON DUPLICATE KEY UPDATE role_id = role_id";
        
        return $this->db->execute($sql, [
            'role_id' => $roleId,
            'permission_id' => $permissionId
        ]);
    }
    
    public function removePermission($roleId, $permissionId)
    {
        $sql = "DELETE FROM role_permissions 
                WHERE role_id = :role_id AND permission_id = :permission_id";
        
        return $this->db->execute($sql, [
            'role_id' => $roleId,
            'permission_id' => $permissionId
        ]);
    }
}
