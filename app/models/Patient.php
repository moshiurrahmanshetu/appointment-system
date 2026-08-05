<?php

namespace App\Models;

use App\Core\Model;

class Patient extends Model
{
    protected $table = 'patients';
    protected $primaryKey = 'id';
    protected $fillable = [
        'patient_code',
        'user_id',
        'full_name',
        'phone',
        'gender',
        'dob',
        'blood_group',
        'address',
        'emergency_contact',
        'emergency_phone',
        'photo',
        'status',
        'created_by',
        'deleted_at'
    ];
    
    public function generatePatientCode()
    {
        // Get the last patient code
        $sql = "SELECT patient_code FROM patients ORDER BY id DESC LIMIT 1";
        $result = $this->db->fetch($sql);
        
        if ($result) {
            // Extract number from last code (PAT-000001)
            $lastNumber = (int)substr($result['patient_code'], -6);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        // Format as PAT-000001
        return 'PAT-' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }
    
    public function generatePatientUserId()
    {
        do {
            // Generate random user ID like PTX84K92
            $userId = 'PT' . strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 1)) . strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 5)) . substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 2);
        } while ($this->checkUserIdAvailability($userId));
        
        return $userId;
    }
    
    public function findByPatientCode($patientCode)
    {
        return $this->findBy('patient_code', $patientCode);
    }
    
    public function findByUserId($userId)
    {
        return $this->findBy('user_id', $userId);
    }
    
    public function getAllPatients($onlyActive = true, $includeDeleted = false)
    {
        $sql = "SELECT p.*, u.user_id as login_id, u.status as account_status, u.full_name as account_name 
                FROM patients p 
                LEFT JOIN users u ON p.user_id = u.id";
        
        $params = [];
        
        if ($onlyActive) {
            $sql .= " WHERE p.status = 'active'";
        }
        
        if (!$includeDeleted) {
            if ($onlyActive) {
                $sql .= " AND p.deleted_at IS NULL";
            } else {
                $sql .= " WHERE p.deleted_at IS NULL";
            }
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getPatientsByStatus($status)
    {
        $sql = "SELECT p.*, u.user_id as login_id, u.status as account_status 
                FROM patients p 
                LEFT JOIN users u ON p.user_id = u.id 
                WHERE p.status = :status AND p.deleted_at IS NULL
                ORDER BY p.created_at DESC";
        
        return $this->db->fetchAll($sql, ['status' => $status]);
    }
    
    public function searchPatients($searchTerm, $filters = [])
    {
        $sql = "SELECT p.*, u.user_id as login_id, u.status as account_status 
                FROM patients p 
                LEFT JOIN users u ON p.user_id = u.id 
                WHERE p.deleted_at IS NULL";
        
        $params = [];
        
        // Search condition
        if (!empty($searchTerm)) {
            $sql .= " AND (p.patient_code LIKE :search OR p.full_name LIKE :search OR p.phone LIKE :search)";
            $params['search'] = '%' . $searchTerm . '%';
        }
        
        // Filter by status
        if (!empty($filters['status'])) {
            $sql .= " AND p.status = :status";
            $params['status'] = $filters['status'];
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function softDelete($patientId)
    {
        // Get patient user_id to also disable the account
        $patient = $this->find($patientId);
        
        // Soft delete patient
        $result = $this->update($patientId, [
            'deleted_at' => date('Y-m-d H:i:s')
        ]);
        
        // Disable linked user account if exists
        if ($patient && $patient['user_id']) {
            $userModel = new User();
            $userModel->updateStatus($patient['user_id'], 'inactive');
        }
        
        return $result;
    }
    
    public function restore($patientId)
    {
        // Get patient user_id to also enable the account
        $patient = $this->find($patientId);
        
        // Restore patient
        $result = $this->update($patientId, [
            'deleted_at' => null
        ]);
        
        // Enable linked user account if exists
        if ($patient && $patient['user_id']) {
            $userModel = new User();
            $userModel->updateStatus($patient['user_id'], 'active');
        }
        
        return $result;
    }
    
    public function updateStatus($patientId, $status)
    {
        // Get patient user_id to also update account status
        $patient = $this->find($patientId);
        
        // Update patient status
        $result = $this->update($patientId, [
            'status' => $status
        ]);
        
        // Update linked user account status if exists
        if ($patient && $patient['user_id']) {
            $userModel = new User();
            $userModel->updateStatus($patient['user_id'], $status);
        }
        
        return $result;
    }
    
    public function updatePhoto($patientId, $photoPath)
    {
        return $this->update($patientId, [
            'photo' => $photoPath
        ]);
    }
    
    public function checkPatientCodeAvailability($patientCode, $excludeId = null)
    {
        return !$this->exists('patient_code', $patientCode, $excludeId);
    }
    
    public function checkPhoneAvailability($phone, $excludeId = null)
    {
        return !$this->exists('phone', $phone, $excludeId);
    }
    
    public function checkUserIdAvailability($userId)
    {
        $userModel = new User();
        return $userModel->checkUserIdAvailability($userId);
    }
    
    public function linkUserAccount($patientId, $userId)
    {
        return $this->update($patientId, [
            'user_id' => $userId
        ]);
    }
    
    public function unlinkUserAccount($patientId)
    {
        return $this->update($patientId, [
            'user_id' => null
        ]);
    }
}