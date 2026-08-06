<?php

namespace App\Models;

use App\Core\Model;

class Appointment extends Model
{
    protected $table = 'appointments';
    protected $primaryKey = 'id';
    protected $fillable = [
        'appointment_no',
        'patient_id',
        'doctor_id',
        'appointment_date',
        'appointment_time',
        'serial_no',
        'visit_type',
        'priority',
        'status',
        'remarks',
        'created_by',
        'updated_by',
        'deleted_at'
    ];
    
    public function generateAppointmentNo()
    {
        // Get the last appointment number
        $sql = "SELECT appointment_no FROM appointments ORDER BY id DESC LIMIT 1";
        $result = $this->db->fetch($sql);
        
        if ($result) {
            // Extract number from last appointment number (APT-000001)
            $lastNumber = (int)substr($result['appointment_no'], -6);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        // Format as APT-000001
        return 'APT-' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }
    
    public function generateSerialNo($doctorId, $appointmentDate)
    {
        // Get the last serial number for this doctor on this date
        $sql = "SELECT MAX(serial_no) as max_serial 
                FROM appointments 
                WHERE doctor_id = :doctor_id 
                AND appointment_date = :appointment_date";
        
        $result = $this->db->fetch($sql, [
            'doctor_id' => $doctorId,
            'appointment_date' => $appointmentDate
        ]);
        
        if ($result && $result['max_serial']) {
            return $result['max_serial'] + 1;
        }
        
        return 1; // Start from 1 for first appointment of the day
    }
    
    public function findByAppointmentNo($appointmentNo)
    {
        return $this->findBy('appointment_no', $appointmentNo);
    }
    
    public function getAllAppointments($onlyActive = true, $includeDeleted = false)
    {
        $sql = "SELECT a.*, 
                p.full_name as patient_name, p.patient_code, p.phone as patient_phone,
                u.full_name as doctor_name, u.user_id as doctor_user_id
                FROM appointments a 
                LEFT JOIN patients p ON a.patient_id = p.id
                LEFT JOIN users u ON a.doctor_id = u.id";
        
        $params = [];
        
        if ($onlyActive) {
            $sql .= " WHERE a.status != 'Cancelled'";
        }
        
        if (!$includeDeleted) {
            if ($onlyActive) {
                $sql .= " AND a.deleted_at IS NULL";
            } else {
                $sql .= " WHERE a.deleted_at IS NULL";
            }
        }
        
        $sql .= " ORDER BY a.appointment_date DESC, a.appointment_time DESC, a.serial_no ASC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getAppointmentsByDate($date)
    {
        $sql = "SELECT a.*, 
                p.full_name as patient_name, p.patient_code,
                u.full_name as doctor_name
                FROM appointments a 
                LEFT JOIN patients p ON a.patient_id = p.id
                LEFT JOIN users u ON a.doctor_id = u.id
                WHERE a.appointment_date = :date 
                AND a.deleted_at IS NULL
                ORDER BY a.serial_no ASC";
        
        return $this->db->fetchAll($sql, ['date' => $date]);
    }
    
    public function getAppointmentsByDoctor($doctorId, $date = null)
    {
        $sql = "SELECT a.*, 
                p.full_name as patient_name, p.patient_code, p.phone as patient_phone,
                u.full_name as doctor_name
                FROM appointments a 
                LEFT JOIN patients p ON a.patient_id = p.id
                LEFT JOIN users u ON a.doctor_id = u.id
                WHERE a.doctor_id = :doctor_id 
                AND a.deleted_at IS NULL";
        
        $params = ['doctor_id' => $doctorId];
        
        if ($date) {
            $sql .= " AND a.appointment_date = :date";
            $params['date'] = $date;
        }
        
        $sql .= " ORDER BY a.appointment_date DESC, a.serial_no ASC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getAppointmentsByPatient($patientId)
    {
        $sql = "SELECT a.*, 
                p.full_name as patient_name, p.patient_code,
                u.full_name as doctor_name
                FROM appointments a 
                LEFT JOIN patients p ON a.patient_id = p.id
                LEFT JOIN users u ON a.doctor_id = u.id
                WHERE a.patient_id = :patient_id 
                AND a.deleted_at IS NULL
                ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        
        return $this->db->fetchAll($sql, ['patient_id' => $patientId]);
    }
    
    public function getAppointmentsByStatus($status)
    {
        $sql = "SELECT a.*, 
                p.full_name as patient_name, p.patient_code,
                u.full_name as doctor_name
                FROM appointments a 
                LEFT JOIN patients p ON a.patient_id = p.id
                LEFT JOIN users u ON a.doctor_id = u.id
                WHERE a.status = :status 
                AND a.deleted_at IS NULL
                ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        
        return $this->db->fetchAll($sql, ['status' => $status]);
    }
    
    public function searchAppointments($searchTerm, $filters = [])
    {
        $sql = "SELECT a.*, 
                p.full_name as patient_name, p.patient_code, p.phone as patient_phone,
                u.full_name as doctor_name, u.user_id as doctor_user_id
                FROM appointments a 
                LEFT JOIN patients p ON a.patient_id = p.id
                LEFT JOIN users u ON a.doctor_id = u.id
                WHERE a.deleted_at IS NULL";
        
        $params = [];
        
        // Search condition
        if (!empty($searchTerm)) {
            $sql .= " AND (a.appointment_no LIKE :search 
                    OR p.full_name LIKE :search 
                    OR p.patient_code LIKE :search 
                    OR p.phone LIKE :search
                    OR u.full_name LIKE :search)";
            $params['search'] = '%' . $searchTerm . '%';
        }
        
        // Filter by doctor
        if (!empty($filters['doctor_id'])) {
            $sql .= " AND a.doctor_id = :doctor_id";
            $params['doctor_id'] = $filters['doctor_id'];
        }
        
        // Filter by date
        if (!empty($filters['appointment_date'])) {
            $sql .= " AND a.appointment_date = :appointment_date";
            $params['appointment_date'] = $filters['appointment_date'];
        }
        
        // Filter by status
        if (!empty($filters['status'])) {
            $sql .= " AND a.status = :status";
            $params['status'] = $filters['status'];
        }
        
        // Filter by priority
        if (!empty($filters['priority'])) {
            $sql .= " AND a.priority = :priority";
            $params['priority'] = $filters['priority'];
        }
        
        $sql .= " ORDER BY a.appointment_date DESC, a.appointment_time DESC, a.serial_no ASC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function softDelete($appointmentId)
    {
        return $this->update($appointmentId, [
            'deleted_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    public function restore($appointmentId)
    {
        return $this->update($appointmentId, [
            'deleted_at' => null
        ]);
    }
    
    public function updateStatus($appointmentId, $status)
    {
        return $this->update($appointmentId, [
            'status' => $status
        ]);
    }
    
    public function checkAppointmentNoAvailability($appointmentNo, $excludeId = null)
    {
        return !$this->exists('appointment_no', $appointmentNo, $excludeId);
    }
    
    public function checkAppointmentAvailability($doctorId, $appointmentDate, $appointmentTime, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as count 
                FROM appointments 
                WHERE doctor_id = :doctor_id 
                AND appointment_date = :appointment_date 
                AND appointment_time = :appointment_time 
                AND status != 'Cancelled'";
        
        $params = [
            'doctor_id' => $doctorId,
            'appointment_date' => $appointmentDate,
            'appointment_time' => $appointmentTime
        ];
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['count'] == 0;
    }
    
    public function getTodayAppointments($doctorId = null)
    {
        $today = date('Y-m-d');
        
        $sql = "SELECT a.*, 
                p.full_name as patient_name, p.patient_code,
                u.full_name as doctor_name
                FROM appointments a 
                LEFT JOIN patients p ON a.patient_id = p.id
                LEFT JOIN users u ON a.doctor_id = u.id
                WHERE a.appointment_date = :today 
                AND a.deleted_at IS NULL
                AND a.status != 'Cancelled'";
        
        $params = ['today' => $today];
        
        if ($doctorId) {
            $sql .= " AND a.doctor_id = :doctor_id";
            $params['doctor_id'] = $doctorId;
        }
        
        $sql .= " ORDER BY a.serial_no ASC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getUpcomingAppointments($limit = 10)
    {
        $sql = "SELECT a.*, 
                p.full_name as patient_name, p.patient_code,
                u.full_name as doctor_name
                FROM appointments a 
                LEFT JOIN patients p ON a.patient_id = p.id
                LEFT JOIN users u ON a.doctor_id = u.id
                WHERE a.appointment_date >= CURDATE() 
                AND a.deleted_at IS NULL 
                AND a.status IN ('Pending', 'Confirmed')
                ORDER BY a.appointment_date ASC, a.appointment_time ASC, a.serial_no ASC
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, ['limit' => $limit]);
    }
    
    public function getAppointmentWithDetails($appointmentId)
    {
        $sql = "SELECT a.*, 
                p.full_name as patient_name, p.patient_code, p.phone as patient_phone, p.gender as patient_gender, p.dob as patient_dob,
                u.full_name as doctor_name, u.user_id as doctor_user_id,
                c.full_name as created_by_name,
                upd.full_name as updated_by_name
                FROM appointments a 
                LEFT JOIN patients p ON a.patient_id = p.id
                LEFT JOIN users u ON a.doctor_id = u.id
                LEFT JOIN users c ON a.created_by = c.id
                LEFT JOIN users upd ON a.updated_by = upd.id
                WHERE a.id = :id";
        
        return $this->db->fetch($sql, ['id' => $appointmentId]);
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