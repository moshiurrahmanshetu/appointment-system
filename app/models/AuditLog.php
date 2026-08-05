<?php

namespace App\Models;

use App\Core\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent'
    ];
    
    public function log($userId, $action, $entityType, $entityId = null, $oldValues = null, $newValues = null)
    {
        $data = [
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ];
        
        return $this->create($data);
    }
    
    public function getUser($auditId)
    {
        $sql = "SELECT u.* FROM users u 
                INNER JOIN audit_logs a ON a.user_id = u.id 
                WHERE a.id = :audit_id";
        
        return $this->db->fetch($sql, ['audit_id' => $auditId]);
    }
    
    public function getByUserId($userId, $limit = 50)
    {
        $sql = "SELECT * FROM audit_logs 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC 
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, ['user_id' => $userId, 'limit' => $limit]);
    }
    
    public function getByEntity($entityType, $entityId, $limit = 50)
    {
        $sql = "SELECT * FROM audit_logs 
                WHERE entity_type = :entity_type AND entity_id = :entity_id 
                ORDER BY created_at DESC 
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, ['entity_type' => $entityType, 'entity_id' => $entityId, 'limit' => $limit]);
    }
    
    public function getAll($limit = 100)
    {
        $sql = "SELECT * FROM audit_logs 
                ORDER BY created_at DESC 
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, ['limit' => $limit]);
    }
}