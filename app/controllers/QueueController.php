<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Session;
use App\Models\AuditLog;
use App\Models\Queue;
use App\Models\User;

class QueueController extends Controller
{
    private $queueModel;
    private $auditModel;
    private $userModel;
    protected $db;
    
    public function __construct()
    {
        parent::__construct();
        $this->queueModel = new Queue();
        $this->auditModel = new AuditLog();
        $this->userModel = new User();
        $this->db = \App\Core\Database::getInstance();
    }
    
    private function getDoctorIdForCurrentUser()
    {
        $currentUser = Session::get('user');
        
        // Super admin sees all queues
        if ($currentUser['role_id'] == 1) { // Assuming Super Admin has role_id 1
            return null;
        }
        
        // Doctor sees only their own queue
        if ($currentUser['role_id'] == 4) { // Assuming Doctor has role_id 4
            return $currentUser['id'];
        }
        
        // Receptionist can see all queues but with limited actions
        return null;
    }
    
    public function index()
    {
        // Check permission
        if (!can('queue.view')) {
            abort(403, 'You do not have permission to view queue.');
        }
        
        $search = $this->input('search', '');
        $filters = [
            'doctor_id' => $this->input('doctor_id'),
            'queue_date' => $this->input('queue_date'),
            'queue_status' => $this->input('queue_status'),
            'priority' => $this->input('priority')
        ];
        
        // Apply doctor-specific view logic
        $doctorId = $this->getDoctorIdForCurrentUser();
        if ($doctorId) {
            $filters['doctor_id'] = $doctorId;
        }
        
        if (!empty($search) || !empty(array_filter($filters))) {
            $queue = $this->queueModel->searchQueue($search, $filters);
        } else {
            $queue = $this->queueModel->getAllQueue($doctorId);
        }
        
        // Get doctors for filter dropdown (only if not doctor user)
        $doctors = null;
        if (!$doctorId) {
            $doctors = $this->db->fetchAll("SELECT u.* FROM users u 
                                              INNER JOIN role_permissions rp ON rp.role_id = u.role_id
                                              INNER JOIN permissions p ON p.id = rp.permission_id
                                              WHERE p.slug = 'appointments.create'
                                              AND u.deleted_at IS NULL
                                              GROUP BY u.id
                                              ORDER BY u.full_name ASC");
        }
        
        // Get queue stats
        $stats = $this->queueModel->getQueueStats($doctorId);
        
        $data = [
            'title' => 'Queue Management - ' . config('name'),
            'queue' => $queue,
            'doctors' => $doctors,
            'search' => $search,
            'filters' => $filters,
            'stats' => $stats,
            'csrf_token' => Csrf::getToken()
        ];
        
        $this->view('queue/index', $data);
    }
    
    public function callNext()
    {
        // Check permission
        if (!can('queue.call')) {
            abort(403, 'You do not have permission to call patients.');
        }
        
        $currentUser = Session::get('user');
        $doctorId = $this->getDoctorIdForCurrentUser();
        
        // If not super admin, use current user's ID
        if (!$doctorId) {
            $doctorId = $this->input('doctor_id');
            if (!$doctorId) {
                Session::setFlash('error', 'Please select a doctor.');
                back();
            }
        }
        
        // Call next patient
        $queue = $this->queueModel->callNext($doctorId);
        
        if ($queue) {
            // Log audit
            $this->auditModel->log(
                $currentUser['id'],
                'patient_called',
                'Queue',
                $queue['id'],
                ['queue_status' => 'Waiting'],
                ['queue_status' => 'Called', 'called_at' => date('Y-m-d H:i:s')]
            );
            
            Session::setFlash('success', 'Called token ' . $queue['token_no'] . ' - ' . $queue['appointment_no']);
        } else {
            Session::setFlash('error', 'No patients waiting in queue.');
        }
        
        redirect('/queue');
    }
    
    public function callSpecific($id)
    {
        // Check permission
        if (!can('queue.call')) {
            abort(403, 'You do not have permission to call patients.');
        }
        
        $currentUser = Session::get('user');
        
        // Check if user can access this queue entry
        $queue = $this->queueModel->find($id);
        if (!$queue) {
            abort(404, 'Queue entry not found.');
        }
        
        $doctorId = $this->getDoctorIdForCurrentUser();
        if ($doctorId && $queue['doctor_id'] != $doctorId) {
            abort(403, 'You can only call patients from your own queue.');
        }
        
        // Call specific patient
        $result = $this->queueModel->callSpecific($id);
        
        if ($result) {
            // Log audit
            $this->auditModel->log(
                $currentUser['id'],
                'patient_called',
                'Queue',
                $id,
                ['queue_status' => 'Waiting'],
                ['queue_status' => 'Called', 'called_at' => date('Y-m-d H:i:s')]
            );
            
            Session::setFlash('success', 'Called token ' . $queue['token_no'] . ' - ' . $queue['appointment_no']);
        } else {
            Session::setFlash('error', 'Could not call patient. Invalid status or already called.');
        }
        
        redirect('/queue');
    }
    
    public function startConsultation($id)
    {
        // Check permission
        if (!can('queue.manage')) {
            abort(403, 'You do not have permission to start consultation.');
        }
        
        $currentUser = Session::get('user');
        
        // Check if user can access this queue entry
        $queue = $this->queueModel->find($id);
        if (!$queue) {
            abort(404, 'Queue entry not found.');
        }
        
        $doctorId = $this->getDoctorIdForCurrentUser();
        if ($doctorId && $queue['doctor_id'] != $doctorId) {
            abort(403, 'You can only manage your own queue.');
        }
        
        // Start consultation - update queue status
        $result = $this->queueModel->startConsultation($id);
        
        if ($result) {
            // Log audit
            $this->auditModel->log(
                $currentUser['id'],
                'consultation_started',
                'Queue',
                $id,
                ['queue_status' => 'Called'],
                ['queue_status' => 'With Doctor', 'started_at' => date('Y-m-d H:i:s')]
            );
            
            // Redirect to consultation creation page
            redirect('/consultations/create?queue_id=' . $id);
        } else {
            Session::setFlash('error', 'Could not start consultation. Invalid status transition.');
            redirect('/queue');
        }
    }
    
    public function completeQueue($id)
    {
        // Check permission
        if (!can('queue.complete')) {
            abort(403, 'You do not have permission to complete queue entries.');
        }
        
        $currentUser = Session::get('user');
        
        // Check if user can access this queue entry
        $queue = $this->queueModel->find($id);
        if (!$queue) {
            abort(404, 'Queue entry not found.');
        }
        
        $doctorId = $this->getDoctorIdForCurrentUser();
        if ($doctorId && $queue['doctor_id'] != $doctorId) {
            abort(403, 'You can only complete patients from your own queue.');
        }
        
        // Complete queue
        $result = $this->queueModel->completeQueue($id);
        
        if ($result) {
            // Log audit
            $this->auditModel->log(
                $currentUser['id'],
                'queue_completed',
                'Queue',
                $id,
                ['queue_status' => 'With Doctor'],
                ['queue_status' => 'Completed', 'completed_at' => date('Y-m-d H:i:s')]
            );
            
            Session::setFlash('success', 'Queue completed for token ' . $queue['token_no']);
        } else {
            Session::setFlash('error', 'Could not complete queue. Invalid status transition.');
        }
        
        redirect('/queue');
    }
    
    public function skipQueue($id)
    {
        // Check permission
        if (!can('queue.skip')) {
            abort(403, 'You do not have permission to skip queue entries.');
        }
        
        $currentUser = Session::get('user');
        
        // Check if user can access this queue entry
        $queue = $this->queueModel->find($id);
        if (!$queue) {
            abort(404, 'Queue entry not found.');
        }
        
        $doctorId = $this->getDoctorIdForCurrentUser();
        if ($doctorId && $queue['doctor_id'] != $doctorId) {
            abort(403, 'You can only skip patients from your own queue.');
        }
        
        // Skip queue
        $result = $this->queueModel->skipQueue($id);
        
        if ($result) {
            // Log audit
            $this->auditModel->log(
                $currentUser['id'],
                'queue_skipped',
                'Queue',
                $id,
                ['queue_status' => $queue['queue_status']],
                ['queue_status' => 'Skipped']
            );
            
            Session::setFlash('success', 'Queue skipped for token ' . $queue['token_no']);
        } else {
            Session::setFlash('error', 'Could not skip queue. Invalid status transition.');
        }
        
        redirect('/queue');
    }
    
    public function cancelQueue($id)
    {
        // Check permission
        if (!can('queue.cancel')) {
            abort(403, 'You do not have permission to cancel queue entries.');
        }
        
        $currentUser = Session::get('user');
        
        // Check if user can access this queue entry
        $queue = $this->queueModel->find($id);
        if (!$queue) {
            abort(404, 'Queue entry not found.');
        }
        
        $doctorId = $this->getDoctorIdForCurrentUser();
        if ($doctorId && $queue['doctor_id'] != $doctorId) {
            abort(403, 'You can only cancel patients from your own queue.');
        }
        
        // Cancel queue
        $result = $this->queueModel->cancelQueue($id);
        
        if ($result) {
            // Log audit
            $this->auditModel->log(
                $currentUser['id'],
                'queue_cancelled',
                'Queue',
                $id,
                ['queue_status' => $queue['queue_status']],
                ['queue_status' => 'Cancelled']
            );
            
            Session::setFlash('success', 'Queue cancelled for token ' . $queue['token_no']);
        } else {
            Session::setFlash('error', 'Could not cancel queue. Invalid status transition.');
        }
        
        redirect('/queue');
    }
    
    public function show($id)
    {
        // Check permission
        if (!can('queue.view')) {
            abort(403, 'You do not have permission to view queue details.');
        }
        
        $queue = $this->queueModel->getQueueWithDetails($id);
        if (!$queue) {
            abort(404, 'Queue entry not found.');
        }
        
        // Check if user can access this queue entry
        $doctorId = $this->getDoctorIdForCurrentUser();
        if ($doctorId && $queue['doctor_id'] != $doctorId) {
            abort(403, 'You can only view your own queue.');
        }
        
        $data = [
            'title' => 'Queue Details - ' . config('name'),
            'queue' => $queue,
            'csrf_token' => Csrf::getToken()
        ];
        
        $this->view('queue/show', $data);
    }
}