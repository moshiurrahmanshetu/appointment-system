<?php

namespace App\Models;

use App\Core\Model;

class Queue extends Model
{
    protected $table = 'queue';
    protected $primaryKey = 'id';
    protected $fillable = [
        'appointment_id',
        'doctor_id',
        'queue_date',
        'token_no',
        'queue_status',
        'called_at',
        'started_at',
        'completed_at',
        'remarks',
        'created_by',
        'updated_by'
    ];
    
    // Priority order for sorting
    private $priorityOrder = [
        'Emergency' => 1,
        'Urgent' => 2,
        'Normal' => 3
    ];
    
    // Valid status transitions
    private $validTransitions = [
        'Waiting' => ['Called', 'Cancelled'],
        'Called' => ['With Doctor', 'Skipped', 'Cancelled'],
        'With Doctor' => ['Completed', 'Cancelled'],
        'Completed' => [], // Cannot transition from completed
        'Skipped' => ['Called', 'Cancelled'], // Can re-call skipped patients
        'Cancelled' => [] // Cannot transition from cancelled
    ];
    
    public function generateTokenNo($doctorId, $queueDate)
    {
        // Get the last token number for this doctor on this date
        $sql = "SELECT token_no FROM queue 
                WHERE doctor_id = :doctor_id 
                AND queue_date = :queue_date 
                ORDER BY id DESC LIMIT 1";
        
        $result = $this->db->fetch($sql, [
            'doctor_id' => $doctorId,
            'queue_date' => $queueDate
        ]);
        
        if ($result) {
            // Extract number from last token (Q-001)
            $lastNumber = (int)substr($result['token_no'], -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        // Format as Q-001
        return 'Q-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
    
    public function createFromAppointment($appointmentId)
    {
        // Check if queue entry already exists for this appointment
        $existing = $this->findBy('appointment_id', $appointmentId);
        if ($existing) {
            return false; // Duplicate queue entry
        }
        
        // Get appointment details
        $sql = "SELECT a.*, p.patient_code 
                FROM appointments a 
                LEFT JOIN patients p ON a.patient_id = p.id 
                WHERE a.id = :id AND a.deleted_at IS NULL";
        
        $appointment = $this->db->fetch($sql, ['id' => $appointmentId]);
        if (!$appointment) {
            return false;
        }
        
        // Generate token
        $tokenNo = $this->generateTokenNo($appointment['doctor_id'], $appointment['appointment_date']);
        
        // Create queue entry
        $queueData = [
            'appointment_id' => $appointmentId,
            'doctor_id' => $appointment['doctor_id'],
            'queue_date' => $appointment['appointment_date'],
            'token_no' => $tokenNo,
            'queue_status' => 'Waiting'
        ];
        
        return $this->create($queueData);
    }
    
    public function getAllQueue($doctorId = null, $queueDate = null)
    {
        $sql = "SELECT q.*, 
                a.appointment_no, a.appointment_time, a.priority,
                p.full_name as patient_name, p.patient_code, p.phone as patient_phone,
                u.full_name as doctor_name
                FROM queue q 
                LEFT JOIN appointments a ON q.appointment_id = a.id
                LEFT JOIN patients p ON a.patient_id = p.id
                LEFT JOIN users u ON q.doctor_id = u.id
                WHERE 1=1";
        
        $params = [];
        
        if ($doctorId) {
            $sql .= " AND q.doctor_id = :doctor_id";
            $params['doctor_id'] = $doctorId;
        }
        
        if ($queueDate) {
            $sql .= " AND q.queue_date = :queue_date";
            $params['queue_date'] = $queueDate;
        }
        
        // Order by priority then token number
        $sql .= " ORDER BY q.queue_date DESC, 
                  FIELD(a.priority, 'Emergency', 'Urgent', 'Normal') ASC,
                  q.token_no ASC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getQueueByStatus($status, $doctorId = null)
    {
        $sql = "SELECT q.*, 
                a.appointment_no, a.appointment_time, a.priority,
                p.full_name as patient_name, p.patient_code,
                u.full_name as doctor_name
                FROM queue q 
                LEFT JOIN appointments a ON q.appointment_id = a.id
                LEFT JOIN patients p ON a.patient_id = p.id
                LEFT JOIN users u ON q.doctor_id = u.id
                WHERE q.queue_status = :status";
        
        $params = ['status' => $status];
        
        if ($doctorId) {
            $sql .= " AND q.doctor_id = :doctor_id";
            $params['doctor_id'] = $doctorId;
        }
        
        $sql .= " ORDER BY FIELD(a.priority, 'Emergency', 'Urgent', 'Normal') ASC, q.token_no ASC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getWaitingQueue($doctorId = null)
    {
        return $this->getQueueByStatus('Waiting', $doctorId);
    }
    
    public function getCalledQueue($doctorId = null)
    {
        return $this->getQueueByStatus('Called', $doctorId);
    }
    
    public function getWithDoctorQueue($doctorId = null)
    {
        return $this->getQueueByStatus('With Doctor', $doctorId);
    }
    
    public function getTodayQueue($doctorId = null)
    {
        $today = date('Y-m-d');
        return $this->getAllQueue($doctorId, $today);
    }
    
    public function searchQueue($searchTerm, $filters = [])
    {
        $sql = "SELECT q.*, 
                a.appointment_no, a.appointment_time, a.priority,
                p.full_name as patient_name, p.patient_code, p.phone as patient_phone,
                u.full_name as doctor_name
                FROM queue q 
                LEFT JOIN appointments a ON q.appointment_id = a.id
                LEFT JOIN patients p ON a.patient_id = p.id
                LEFT JOIN users u ON q.doctor_id = u.id
                WHERE 1=1";
        
        $params = [];
        
        // Search condition
        if (!empty($searchTerm)) {
            $sql .= " AND (q.token_no LIKE :search 
                    OR a.appointment_no LIKE :search 
                    OR p.full_name LIKE :search 
                    OR p.patient_code LIKE :search
                    OR u.full_name LIKE :search)";
            $params['search'] = '%' . $searchTerm . '%';
        }
        
        // Filter by doctor
        if (!empty($filters['doctor_id'])) {
            $sql .= " AND q.doctor_id = :doctor_id";
            $params['doctor_id'] = $filters['doctor_id'];
        }
        
        // Filter by date
        if (!empty($filters['queue_date'])) {
            $sql .= " AND q.queue_date = :queue_date";
            $params['queue_date'] = $filters['queue_date'];
        }
        
        // Filter by status
        if (!empty($filters['queue_status'])) {
            $sql .= " AND q.queue_status = :queue_status";
            $params['queue_status'] = $filters['queue_status'];
        }
        
        // Filter by priority
        if (!empty($filters['priority'])) {
            $sql .= " AND a.priority = :priority";
            $params['priority'] = $filters['priority'];
        }
        
        // Order by priority then token
        $sql .= " ORDER BY q.queue_date DESC, 
                  FIELD(a.priority, 'Emergency', 'Urgent', 'Normal') ASC,
                  q.token_no ASC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function isValidStatusTransition($currentStatus, $newStatus)
    {
        if (!isset($this->validTransitions[$currentStatus])) {
            return false;
        }
        
        return in_array($newStatus, $this->validTransitions[$currentStatus]);
    }
    
    public function updateStatus($queueId, $newStatus)
    {
        $queue = $this->find($queueId);
        if (!$queue) {
            return false;
        }
        
        // Validate status transition
        if (!$this->isValidStatusTransition($queue['queue_status'], $newStatus)) {
            return false;
        }
        
        // Update timestamps based on status
        $updateData = ['queue_status' => $newStatus];
        
        switch ($newStatus) {
            case 'Called':
                $updateData['called_at'] = date('Y-m-d H:i:s');
                break;
            case 'With Doctor':
                $updateData['started_at'] = date('Y-m-d H:i:s');
                break;
            case 'Completed':
                $updateData['completed_at'] = date('Y-m-d H:i:s');
                break;
        }
        
        return $this->update($queueId, $updateData);
    }
    
    public function callNext($doctorId)
    {
        // Get next waiting patient for this doctor today
        $sql = "SELECT q.* 
                FROM queue q 
                LEFT JOIN appointments a ON q.appointment_id = a.id
                WHERE q.doctor_id = :doctor_id 
                AND q.queue_date = CURDATE()
                AND q.queue_status = 'Waiting'
                ORDER BY FIELD(a.priority, 'Emergency', 'Urgent', 'Normal') ASC, q.token_no ASC
                LIMIT 1";
        
        $queue = $this->db->fetch($sql, ['doctor_id' => $doctorId]);
        
        if (!$queue) {
            return false;
        }
        
        // Call the patient
        if ($this->updateStatus($queue['id'], 'Called')) {
            return $queue;
        }
        
        return false;
    }
    
    public function callSpecific($queueId)
    {
        $queue = $this->find($queueId);
        if (!$queue || $queue['queue_status'] !== 'Waiting') {
            return false;
        }
        
        if ($this->updateStatus($queueId, 'Called')) {
            return $queue;
        }
        
        return false;
    }
    
    public function startConsultation($queueId)
    {
        return $this->updateStatus($queueId, 'With Doctor');
    }
    
    public function completeQueue($queueId)
    {
        return $this->updateStatus($queueId, 'Completed');
    }
    
    public function skipQueue($queueId)
    {
        return $this->updateStatus($queueId, 'Skipped');
    }
    
    public function cancelQueue($queueId)
    {
        return $this->updateStatus($queueId, 'Cancelled');
    }
    
    public function getQueueWithDetails($queueId)
    {
        $sql = "SELECT q.*, 
                a.appointment_no, a.appointment_time, a.priority, a.remarks as appointment_remarks,
                p.full_name as patient_name, p.patient_code, p.phone as patient_phone, p.gender as patient_gender, p.dob as patient_dob,
                u.full_name as doctor_name, u.user_id as doctor_user_id,
                c.full_name as created_by_name,
                upd.full_name as updated_by_name
                FROM queue q 
                LEFT JOIN appointments a ON q.appointment_id = a.id
                LEFT JOIN patients p ON a.patient_id = p.id
                LEFT JOIN users u ON q.doctor_id = u.id
                LEFT JOIN users c ON q.created_by = c.id
                LEFT JOIN users upd ON q.updated_by = upd.id
                WHERE q.id = :id";
        
        return $this->db->fetch($sql, ['id' => $queueId]);
    }
    
    public function getQueueStats($doctorId = null)
    {
        $today = date('Y-m-d');
        
        $sql = "SELECT 
                COUNT(CASE WHEN queue_status = 'Waiting' THEN 1 END) as waiting,
                COUNT(CASE WHEN queue_status = 'Called' THEN 1 END) as called,
                COUNT(CASE WHEN queue_status = 'With Doctor' THEN 1 END) as with_doctor,
                COUNT(CASE WHEN queue_status = 'Completed' THEN 1 END) as completed,
                COUNT(CASE WHEN queue_status = 'Skipped' THEN 1 END) as skipped,
                COUNT(CASE WHEN queue_status = 'Cancelled' THEN 1 END) as cancelled
                FROM queue 
                WHERE queue_date = :queue_date";
        
        $params = ['queue_date' => $today];
        
        if ($doctorId) {
            $sql .= " AND doctor_id = :doctor_id";
            $params['doctor_id'] = $doctorId;
        }
        
        return $this->db->fetch($sql, $params);
    }
    
    public function getWaitingCount($doctorId = null)
    {
        $stats = $this->getQueueStats($doctorId);
        return $stats ? $stats['waiting'] : 0;
    }
    
    public function getWithDoctorCount($doctorId = null)
    {
        $stats = $this->getQueueStats($doctorId);
        return $stats ? $stats['with_doctor'] : 0;
    }
    
    public function getCompletedTodayCount($doctorId = null)
    {
        $stats = $this->getQueueStats($doctorId);
        return $stats ? $stats['completed'] : 0;
    }
    
    public function getSkippedTodayCount($doctorId = null)
    {
        $stats = $this->getQueueStats($doctorId);
        return $stats ? $stats['skipped'] : 0;
    }
    
    public function findByAppointmentId($appointmentId)
    {
        return $this->findBy('appointment_id', $appointmentId);
    }
    
    public function checkQueueExists($appointmentId)
    {
        return $this->exists('appointment_id', $appointmentId);
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