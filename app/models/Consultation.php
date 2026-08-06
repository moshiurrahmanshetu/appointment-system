<?php

namespace App\Models;

use App\Core\Model;

class Consultation extends Model
{
    protected $table = 'consultations';
    protected $primaryKey = 'id';
    protected $fillable = [
        'consultation_no',
        'appointment_id',
        'queue_id',
        'patient_id',
        'doctor_id',
        'visit_type',
        'chief_complaint',
        'history',
        'physical_examination',
        'diagnosis',
        'doctor_notes',
        'follow_up_required',
        'follow_up_date',
        'consultation_status',
        'created_by',
        'updated_by',
        'deleted_at'
    ];
    
    public function generateConsultationNo()
    {
        // Get the last consultation number
        $sql = "SELECT consultation_no FROM consultations ORDER BY id DESC LIMIT 1";
        $result = $this->db->fetch($sql);
        
        if ($result) {
            // Extract number from last consultation number (CON-000001)
            $lastNumber = (int)substr($result['consultation_no'], -6);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        // Format as CON-000001
        return 'CON-' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }
    
    public function findByConsultationNo($consultationNo)
    {
        return $this->findBy('consultation_no', $consultationNo);
    }
    
    public function getAllConsultations($onlyActive = true, $includeDeleted = false)
    {
        $sql = "SELECT c.*, 
                p.full_name as patient_name, p.patient_code, p.phone as patient_phone,
                u.full_name as doctor_name, u.user_id as doctor_user_id,
                a.appointment_no, a.appointment_date, a.appointment_time,
                q.token_no, q.queue_status
                FROM consultations c 
                LEFT JOIN patients p ON c.patient_id = p.id
                LEFT JOIN users u ON c.doctor_id = u.id
                LEFT JOIN appointments a ON c.appointment_id = a.id
                LEFT JOIN queue q ON c.queue_id = q.id";
        
        $params = [];
        
        if ($onlyActive) {
            $sql .= " WHERE c.consultation_status != 'Cancelled'";
        }
        
        if (!$includeDeleted) {
            if ($onlyActive) {
                $sql .= " AND c.deleted_at IS NULL";
            } else {
                $sql .= " WHERE c.deleted_at IS NULL";
            }
        }
        
        $sql .= " ORDER BY c.created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getConsultationsByPatient($patientId)
    {
        $sql = "SELECT c.*, 
                p.full_name as patient_name, p.patient_code,
                u.full_name as doctor_name, u.user_id as doctor_user_id,
                a.appointment_no, a.appointment_date, a.appointment_time,
                q.token_no
                FROM consultations c 
                LEFT JOIN patients p ON c.patient_id = p.id
                LEFT JOIN users u ON c.doctor_id = u.id
                LEFT JOIN appointments a ON c.appointment_id = a.id
                LEFT JOIN queue q ON c.queue_id = q.id
                WHERE c.patient_id = :patient_id 
                AND c.deleted_at IS NULL
                ORDER BY c.created_at DESC";
        
        return $this->db->fetchAll($sql, ['patient_id' => $patientId]);
    }
    
    public function getConsultationsByDoctor($doctorId)
    {
        $sql = "SELECT c.*, 
                p.full_name as patient_name, p.patient_code, p.phone as patient_phone,
                u.full_name as doctor_name, u.user_id as doctor_user_id,
                a.appointment_no, a.appointment_date, a.appointment_time,
                q.token_no
                FROM consultations c 
                LEFT JOIN patients p ON c.patient_id = p.id
                LEFT JOIN users u ON c.doctor_id = u.id
                LEFT JOIN appointments a ON c.appointment_id = a.id
                LEFT JOIN queue q ON c.queue_id = q.id
                WHERE c.doctor_id = :doctor_id 
                AND c.deleted_at IS NULL
                ORDER BY c.created_at DESC";
        
        return $this->db->fetchAll($sql, ['doctor_id' => $doctorId]);
    }
    
    public function getTodayConsultations($doctorId = null)
    {
        $today = date('Y-m-d');
        
        $sql = "SELECT c.*, 
                p.full_name as patient_name, p.patient_code,
                u.full_name as doctor_name,
                a.appointment_no, a.appointment_date, a.appointment_time,
                q.token_no
                FROM consultations c 
                LEFT JOIN patients p ON c.patient_id = p.id
                LEFT JOIN users u ON c.doctor_id = u.id
                LEFT JOIN appointments a ON c.appointment_id = a.id
                LEFT JOIN queue q ON c.queue_id = q.id
                WHERE DATE(c.created_at) = :today 
                AND c.deleted_at IS NULL";
        
        $params = ['today' => $today];
        
        if ($doctorId) {
            $sql .= " AND c.doctor_id = :doctor_id";
            $params['doctor_id'] = $doctorId;
        }
        
        $sql .= " ORDER BY c.created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getFollowUpConsultations($date = null)
    {
        $targetDate = $date ?: date('Y-m-d');
        
        $sql = "SELECT c.*, 
                p.full_name as patient_name, p.patient_code, p.phone as patient_phone,
                u.full_name as doctor_name, u.user_id as doctor_user_id,
                a.appointment_no, a.appointment_date, a.appointment_time
                FROM consultations c 
                LEFT JOIN patients p ON c.patient_id = p.id
                LEFT JOIN users u ON c.doctor_id = u.id
                LEFT JOIN appointments a ON c.appointment_id = a.id
                WHERE c.follow_up_required = 'Yes'
                AND c.follow_up_date = :target_date
                AND c.deleted_at IS NULL
                ORDER BY c.follow_up_date ASC";
        
        return $this->db->fetchAll($sql, ['target_date' => $targetDate]);
    }
    
    public function searchConsultations($searchTerm, $filters = [])
    {
        $sql = "SELECT c.*, 
                p.full_name as patient_name, p.patient_code, p.phone as patient_phone,
                u.full_name as doctor_name, u.user_id as doctor_user_id,
                a.appointment_no, a.appointment_date, a.appointment_time,
                q.token_no
                FROM consultations c 
                LEFT JOIN patients p ON c.patient_id = p.id
                LEFT JOIN users u ON c.doctor_id = u.id
                LEFT JOIN appointments a ON c.appointment_id = a.id
                LEFT JOIN queue q ON c.queue_id = q.id
                WHERE c.deleted_at IS NULL";
        
        $params = [];
        
        // Search condition
        if (!empty($searchTerm)) {
            $sql .= " AND (c.consultation_no LIKE :search 
                    OR p.full_name LIKE :search 
                    OR p.patient_code LIKE :search 
                    OR p.phone LIKE :search
                    OR u.full_name LIKE :search
                    OR c.diagnosis LIKE :search)";
            $params['search'] = '%' . $searchTerm . '%';
        }
        
        // Filter by doctor
        if (!empty($filters['doctor_id'])) {
            $sql .= " AND c.doctor_id = :doctor_id";
            $params['doctor_id'] = $filters['doctor_id'];
        }
        
        // Filter by patient
        if (!empty($filters['patient_id'])) {
            $sql .= " AND c.patient_id = :patient_id";
            $params['patient_id'] = $filters['patient_id'];
        }
        
        // Filter by status
        if (!empty($filters['consultation_status'])) {
            $sql .= " AND c.consultation_status = :consultation_status";
            $params['consultation_status'] = $filters['consultation_status'];
        }
        
        // Filter by visit type
        if (!empty($filters['visit_type'])) {
            $sql .= " AND c.visit_type = :visit_type";
            $params['visit_type'] = $filters['visit_type'];
        }
        
        $sql .= " ORDER BY c.created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function softDelete($consultationId)
    {
        return $this->update($consultationId, [
            'deleted_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    public function restore($consultationId)
    {
        return $this->update($consultationId, [
            'deleted_at' => null
        ]);
    }
    
    public function completeConsultation($consultationId)
    {
        return $this->update($consultationId, [
            'consultation_status' => 'Completed'
        ]);
    }
    
    public function checkConsultationNoAvailability($consultationNo, $excludeId = null)
    {
        return !$this->exists('consultation_no', $consultationNo, $excludeId);
    }
    
    public function checkConsultationExistsForQueue($queueId)
    {
        return $this->exists('queue_id', $queueId);
    }
    
    public function checkConsultationExistsForAppointment($appointmentId)
    {
        return $this->exists('appointment_id', $appointmentId);
    }
    
    public function getConsultationWithDetails($consultationId)
    {
        $sql = "SELECT c.*, 
                p.full_name as patient_name, p.patient_code, p.phone as patient_phone, p.gender as patient_gender, p.dob as patient_dob, p.blood_group as patient_blood_group, p.address as patient_address,
                u.full_name as doctor_name, u.user_id as doctor_user_id,
                a.appointment_no, a.appointment_date, a.appointment_time, a.priority, a.status as appointment_status,
                q.token_no, q.queue_status,
                cby.full_name as created_by_name,
                upd.full_name as updated_by_name
                FROM consultations c 
                LEFT JOIN patients p ON c.patient_id = p.id
                LEFT JOIN users u ON c.doctor_id = u.id
                LEFT JOIN appointments a ON c.appointment_id = a.id
                LEFT JOIN queue q ON c.queue_id = q.id
                LEFT JOIN users cby ON c.created_by = cby.id
                LEFT JOIN users upd ON c.updated_by = upd.id
                WHERE c.id = :id";
        
        return $this->db->fetch($sql, ['id' => $consultationId]);
    }
    
    public function getPatientConsultationHistory($patientId, $limit = 10)
    {
        $sql = "SELECT c.*, 
                u.full_name as doctor_name,
                a.appointment_no, a.appointment_date
                FROM consultations c 
                LEFT JOIN users u ON c.doctor_id = u.id
                LEFT JOIN appointments a ON c.appointment_id = a.id
                WHERE c.patient_id = :patient_id 
                AND c.deleted_at IS NULL
                AND c.consultation_status = 'Completed'
                ORDER BY c.created_at DESC
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, ['patient_id' => $patientId, 'limit' => $limit]);
    }
    
    public function getConsultationStats($doctorId = null)
    {
        $today = date('Y-m-d');
        
        $sql = "SELECT 
                COUNT(CASE WHEN consultation_status = 'Draft' THEN 1 END) as draft,
                COUNT(CASE WHEN consultation_status = 'Completed' THEN 1 END) as completed,
                COUNT(CASE WHEN DATE(created_at) = :today THEN 1 END) as today_total,
                COUNT(CASE WHEN follow_up_required = 'Yes' AND follow_up_date = :today THEN 1 END) as follow_up_today
                FROM consultations 
                WHERE deleted_at IS NULL";
        
        $params = ['today' => $today];
        
        if ($doctorId) {
            $sql .= " AND doctor_id = :doctor_id";
            $params['doctor_id'] = $doctorId;
        }
        
        return $this->db->fetch($sql, $params);
    }
    
    public function count($where = null)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];
        
        if ($where) {
            $sql .= " WHERE " . $where;
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['count'];
    }
}