<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Session;
use App\Models\AuditLog;
use App\Models\Consultation;
use App\Models\Queue;
use App\Models\Appointment;
use App\Models\User;

class ConsultationController extends Controller
{
    private $consultationModel;
    private $queueModel;
    private $appointmentModel;
    private $auditModel;
    private $userModel;
    protected $db;
    
    public function __construct()
    {
        parent::__construct();
        $this->consultationModel = new Consultation();
        $this->queueModel = new Queue();
        $this->appointmentModel = new Appointment();
        $this->auditModel = new AuditLog();
        $this->userModel = new User();
        $this->db = \App\Core\Database::getInstance();
    }
    
    private function getDoctorIdForCurrentUser()
    {
        $currentUser = Session::get('user');
        
        // Super admin sees all consultations
        if ($currentUser['role_id'] == 1) { // Assuming Super Admin has role_id 1
            return null;
        }
        
        // Doctor sees only their own consultations
        if ($currentUser['role_id'] == 4) { // Assuming Doctor has role_id 4
            return $currentUser['id'];
        }
        
        // Receptionist can see all consultations with limited actions
        return null;
    }
    
    public function index()
    {
        // Check permission
        if (!can('consultation.view')) {
            abort(403, 'You do not have permission to view consultations.');
        }
        
        $search = $this->input('search', '');
        $filters = [
            'doctor_id' => $this->input('doctor_id'),
            'patient_id' => $this->input('patient_id'),
            'consultation_status' => $this->input('consultation_status'),
            'visit_type' => $this->input('visit_type')
        ];
        
        // Apply doctor-specific view logic
        $doctorId = $this->getDoctorIdForCurrentUser();
        if ($doctorId) {
            $filters['doctor_id'] = $doctorId;
        }
        
        if (!empty($search) || !empty(array_filter($filters))) {
            $consultations = $this->consultationModel->searchConsultations($search, $filters);
        } else {
            $consultations = $this->consultationModel->getAllConsultations();
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
        
        // Get consultation stats
        $stats = $this->consultationModel->getConsultationStats($doctorId);
        
        $data = [
            'title' => 'Consultations - ' . config('name'),
            'consultations' => $consultations,
            'doctors' => $doctors,
            'search' => $search,
            'filters' => $filters,
            'stats' => $stats,
            'csrf_token' => Csrf::getToken()
        ];
        
        $this->view('consultations/index', $data);
    }
    
    public function create()
    {
        // Check permission
        if (!can('consultation.create')) {
            abort(403, 'You do not have permission to create consultations.');
        }
        
        $queueId = $this->input('queue_id');
        $appointmentId = $this->input('appointment_id');
        
        // Get queue details if provided
        $queue = null;
        $appointment = null;
        $patient = null;
        
        if ($queueId) {
            $queue = $this->queueModel->getQueueWithDetails($queueId);
            if ($queue) {
                // Check if queue status is "With Doctor"
                if ($queue['queue_status'] !== 'With Doctor') {
                    Session::setFlash('error', 'Consultation can only be started for patients with status "With Doctor".');
                    redirect('/queue');
                }
                
                // Check if consultation already exists for this queue
                if ($this->consultationModel->checkConsultationExistsForQueue($queueId)) {
                    Session::setFlash('error', 'Consultation already exists for this queue entry.');
                    redirect('/queue');
                }
                
                $appointmentId = $queue['appointment_id'];
                $appointment = $this->appointmentModel->getAppointmentWithDetails($appointmentId);
            }
        }
        
        if ($appointmentId) {
            $appointment = $this->appointmentModel->getAppointmentWithDetails($appointmentId);
        }
        
        if ($appointment) {
            $patient = $this->db->fetch("SELECT * FROM patients WHERE id = :id", ['id' => $appointment['patient_id']]);
        }
        
        // Get doctors for dropdown
        $doctors = $this->db->fetchAll("SELECT u.* FROM users u 
                                          INNER JOIN role_permissions rp ON rp.role_id = u.role_id
                                          INNER JOIN permissions p ON p.id = rp.permission_id
                                          WHERE p.slug = 'appointments.create'
                                          AND u.deleted_at IS NULL
                                          GROUP BY u.id
                                          ORDER BY u.full_name ASC");
        
        $data = [
            'title' => 'New Consultation - ' . config('name'),
            'queue' => $queue,
            'appointment' => $appointment,
            'patient' => $patient,
            'doctors' => $doctors,
            'csrf_token' => Csrf::getToken()
        ];
        
        $this->view('consultations/create', $data);
    }
    
    public function store()
    {
        // Check permission
        if (!can('consultation.create')) {
            abort(403, 'User does not have permission to create consultations.');
        }
        
        // Validate CSRF token
        if (!Csrf::checkRequest()) {
            Session::setFlash('error', 'Invalid request. Please try again.');
            back();
        }
        
        $currentUser = Session::get('user');
        
        // Validate input
        $errors = $this->validate([
            'patient_id' => 'required',
            'doctor_id' => 'required'
        ]);
        
        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            set_old($this->only(['patient_id', 'doctor_id', 'visit_type', 'chief_complaint', 'history', 'physical_examination', 'diagnosis', 'doctor_notes', 'follow_up_required', 'follow_up_date']));
            back();
        }
        
        // Validate patient exists
        $patientId = $this->input('patient_id');
        $patient = $this->db->fetch("SELECT * FROM patients WHERE id = :id", ['id' => $patientId]);
        if (!$patient || $patient['deleted_at']) {
            Session::setFlash('error', 'Invalid patient selected.');
            set_old($this->only(['patient_id', 'doctor_id', 'visit_type', 'chief_complaint', 'history', 'physical_examination', 'diagnosis', 'doctor_notes', 'follow_up_required', 'follow_up_date']));
            back();
        }
        
        // Validate doctor exists
        $doctorId = $this->input('doctor_id');
        $doctor = $this->userModel->find($doctorId);
        if (!$doctor || $doctor['deleted_at']) {
            Session::setFlash('error', 'Invalid doctor selected.');
            set_old($this->only(['patient_id', 'doctor_id', 'visit_type', 'chief_complaint', 'history', 'physical_examination', 'diagnosis', 'doctor_notes', 'follow_up_required', 'follow_up_date']));
            back();
        }
        
        // Check for duplicate consultation
        $queueId = $this->input('queue_id');
        $appointmentId = $this->input('appointment_id');
        
        if ($queueId && $this->consultationModel->checkConsultationExistsForQueue($queueId)) {
            Session::setFlash('error', 'Consultation already exists for this queue entry.');
            set_old($this->only(['patient_id', 'doctor_id', 'visit_type', 'chief_complaint', 'history', 'physical_examination', 'diagnosis', 'doctor_notes', 'follow_up_required', 'follow_up_date']));
            back();
        }
        
        if ($appointmentId && $this->consultationModel->checkConsultationExistsForAppointment($appointmentId)) {
            Session::setFlash('error', 'Consultation already exists for this appointment.');
            set_old($this->only(['patient_id', 'doctor_id', 'visit_type', 'chief_complaint', 'history', 'physical_examination', 'diagnosis', 'doctor_notes', 'follow_up_required', 'follow_up_date']));
            back();
        }
        
        // Generate consultation number
        $consultationNo = $this->consultationModel->generateConsultationNo();
        
        // Create consultation
        $consultationData = [
            'consultation_no' => $consultationNo,
            'appointment_id' => $appointmentId ?: null,
            'queue_id' => $queueId ?: null,
            'patient_id' => $patientId,
            'doctor_id' => $doctorId,
            'visit_type' => $this->input('visit_type', 'New'),
            'chief_complaint' => $this->input('chief_complaint'),
            'history' => $this->input('history'),
            'physical_examination' => $this->input('physical_examination'),
            'diagnosis' => $this->input('diagnosis'),
            'doctor_notes' => $this->input('doctor_notes'),
            'follow_up_required' => $this->input('follow_up_required', 'No'),
            'follow_up_date' => $this->input('follow_up_date') ?: null,
            'consultation_status' => 'Draft',
            'created_by' => $currentUser['id']
        ];
        
        $consultationId = $this->consultationModel->create($consultationData);
        
        // Log audit
        $this->auditModel->log(
            $currentUser['id'],
            'consultation_started',
            'Consultation',
            $consultationId,
            null,
            $consultationData
        );
        
        Session::setFlash('success', 'Consultation created successfully. Consultation No: ' . $consultationNo);
        redirect('/consultations/show/' . $consultationId);
    }
    
    public function show($id)
    {
        // Check permission
        if (!can('consultation.view')) {
            abort(403, 'You do not have permission to view consultations.');
        }
        
        $consultation = $this->consultationModel->getConsultationWithDetails($id);
        if (!$consultation || $consultation['deleted_at']) {
            abort(404, 'Consultation not found.');
        }
        
        // Check if user can access this consultation
        $doctorId = $this->getDoctorIdForCurrentUser();
        if ($doctorId && $consultation['doctor_id'] != $doctorId) {
            abort(403, 'You can only view your own consultations.');
        }
        
        // Get patient consultation history
        $history = $this->consultationModel->getPatientConsultationHistory($consultation['patient_id'], 10);
        
        $data = [
            'title' => 'Consultation Details - ' . config('name'),
            'consultation' => $consultation,
            'history' => $history,
            'csrf_token' => Csrf::getToken()
        ];
        
        $this->view('consultations/show', $data);
    }
    
    public function edit($id)
    {
        // Check permission
        if (!can('consultation.edit')) {
            abort(403, 'You do not have permission to edit consultations.');
        }
        
        $consultation = $this->consultationModel->getConsultationWithDetails($id);
        if (!$consultation || $consultation['deleted_at']) {
            abort(404, 'Consultation not found.');
        }
        
        // Check if consultation is completed
        if ($consultation['consultation_status'] === 'Completed' && !can('consultation.complete')) {
            Session::setFlash('error', 'Cannot edit completed consultation.');
            redirect('/consultations/show/' . $id);
        }
        
        // Check if user can access this consultation
        $doctorId = $this->getDoctorIdForCurrentUser();
        if ($doctorId && $consultation['doctor_id'] != $doctorId) {
            abort(403, 'You can only edit your own consultations.');
        }
        
        // Get doctors for dropdown
        $doctors = $this->db->fetchAll("SELECT u.* FROM users u 
                                          INNER JOIN role_permissions rp ON rp.role_id = u.role_id
                                          INNER JOIN permissions p ON p.id = rp.permission_id
                                          WHERE p.slug = 'appointments.create'
                                          AND u.deleted_at IS NULL
                                          GROUP BY u.id
                                          ORDER BY u.full_name ASC");
        
        $data = [
            'title' => 'Edit Consultation - ' . config('name'),
            'consultation' => $consultation,
            'doctors' => $doctors,
            'csrf_token' => Csrf::getToken()
        ];
        
        $this->view('consultations/edit', $data);
    }
    
    public function update($id)
    {
        // Check permission
        if (!can('consultation.edit')) {
            abort(403, 'User does not have permission to edit consultations.');
        }
        
        // Validate CSRF token
        if (!Csrf::checkRequest()) {
            Session::setFlash('error', 'Invalid request. Please try again.');
            back();
        }
        
        $currentUser = Session::get('user');
        
        $consultation = $this->consultationModel->getConsultationWithDetails($id);
        if (!$consultation || $consultation['deleted_at']) {
            abort(404, 'Consultation not found.');
        }
        
        // Check if consultation is completed
        if ($consultation['consultation_status'] === 'Completed' && !can('consultation.complete')) {
            Session::setFlash('error', 'Cannot edit completed consultation.');
            redirect('/consultations/show/' . $id);
        }
        
        // Validate input
        $errors = $this->validate([
            'patient_id' => 'required',
            'doctor_id' => 'required'
        ]);
        
        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            set_old($this->only(['patient_id', 'doctor_id', 'visit_type', 'chief_complaint', 'history', 'physical_examination', 'diagnosis', 'doctor_notes', 'follow_up_required', 'follow_up_date', 'consultation_status']));
            back();
        }
        
        // Store old values for audit
        $oldValues = [
            'visit_type' => $consultation['visit_type'],
            'chief_complaint' => $consultation['chief_complaint'],
            'history' => $consultation['history'],
            'physical_examination' => $consultation['physical_examination'],
            'diagnosis' => $consultation['diagnosis'],
            'doctor_notes' => $consultation['doctor_notes'],
            'follow_up_required' => $consultation['follow_up_required'],
            'follow_up_date' => $consultation['follow_up_date'],
            'consultation_status' => $consultation['consultation_status']
        ];
        
        // Update consultation
        $consultationData = [
            'patient_id' => $this->input('patient_id'),
            'doctor_id' => $this->input('doctor_id'),
            'visit_type' => $this->input('visit_type', 'New'),
            'chief_complaint' => $this->input('chief_complaint'),
            'history' => $this->input('history'),
            'physical_examination' => $this->input('physical_examination'),
            'diagnosis' => $this->input('diagnosis'),
            'doctor_notes' => $this->input('doctor_notes'),
            'follow_up_required' => $this->input('follow_up_required', 'No'),
            'follow_up_date' => $this->input('follow_up_date') ?: null,
            'consultation_status' => $this->input('consultation_status', $consultation['consultation_status']),
            'updated_by' => $currentUser['id']
        ];
        
        $this->consultationModel->update($id, $consultationData);
        
        // Check if consultation was just completed
        if ($oldValues['consultation_status'] !== 'Completed' && $consultationData['consultation_status'] === 'Completed') {
            // Auto-complete queue
            if ($consultation['queue_id']) {
                $this->queueModel->completeQueue($consultation['queue_id']);
                
                // Log queue completion
                $this->auditModel->log(
                    $currentUser['id'],
                    'queue_completed',
                    'Queue',
                    $consultation['queue_id'],
                    ['queue_status' => 'With Doctor'],
                    ['queue_status' => 'Completed', 'completed_at' => date('Y-m-d H:i:s')]
                );
            }
            
            // Auto-complete appointment
            if ($consultation['appointment_id']) {
                $this->appointmentModel->updateStatus($consultation['appointment_id'], 'Completed');
                
                // Log appointment completion
                $this->auditModel->log(
                    $currentUser['id'],
                    'appointment_status_changed',
                    'Appointment',
                    $consultation['appointment_id'],
                    ['status' => $consultation['appointment_status']],
                    ['status' => 'Completed']
                );
            }
            
            // Log consultation completion
            $this->auditModel->log(
                $currentUser['id'],
                'consultation_completed',
                'Consultation',
                $id,
                ['consultation_status' => 'Draft'],
                ['consultation_status' => 'Completed']
            );
        } else {
            // Log consultation update
            $this->auditModel->log(
                $currentUser['id'],
                'consultation_updated',
                'Consultation',
                $id,
                $oldValues,
                $consultationData
            );
        }
        
        Session::setFlash('success', 'Consultation updated successfully.');
        redirect('/consultations/show/' . $id);
    }
    
    public function delete($id)
    {
        // Check permission
        if (!can('consultation.delete')) {
            abort(403, 'You do not have permission to delete consultations.');
        }
        
        $consultation = $this->consultationModel->getConsultationWithDetails($id);
        if (!$consultation || $consultation['deleted_at']) {
            abort(404, 'Consultation not found.');
        }
        
        // Soft delete consultation
        $this->consultationModel->softDelete($id);
        
        // Log audit
        $this->auditModel->log(
            Session::get('user')['id'],
            'consultation_deleted',
            'Consultation',
            $id,
            $consultation,
            ['deleted_at' => date('Y-m-d H:i:s')]
        );
        
        Session::setFlash('success', 'Consultation deleted successfully.');
        redirect('/consultations');
    }
    
    public function complete($id)
    {
        // Check permission
        if (!can('consultation.complete')) {
            abort(403, 'You do not have permission to complete consultations.');
        }
        
        $currentUser = Session::get('user');
        
        $consultation = $this->consultationModel->getConsultationWithDetails($id);
        if (!$consultation || $consultation['deleted_at']) {
            abort(404, 'Consultation not found.');
        }
        
        // Complete consultation
        $this->consultationModel->completeConsultation($id);
        
        // Auto-complete queue
        if ($consultation['queue_id']) {
            $this->queueModel->completeQueue($consultation['queue_id']);
            
            // Log queue completion
            $this->auditModel->log(
                $currentUser['id'],
                'queue_completed',
                'Queue',
                $consultation['queue_id'],
                ['queue_status' => 'With Doctor'],
                ['queue_status' => 'Completed', 'completed_at' => date('Y-m-d H:i:s')]
            );
        }
        
        // Auto-complete appointment
        if ($consultation['appointment_id']) {
            $this->appointmentModel->updateStatus($consultation['appointment_id'], 'Completed');
            
            // Log appointment completion
            $this->auditModel->log(
                $currentUser['id'],
                'appointment_status_changed',
                'Appointment',
                $consultation['appointment_id'],
                ['status' => $consultation['appointment_status']],
                ['status' => 'Completed']
            );
        }
        
        // Log consultation completion
        $this->auditModel->log(
            $currentUser['id'],
            'consultation_completed',
            'Consultation',
            $id,
            ['consultation_status' => 'Draft'],
            ['consultation_status' => 'Completed']
        );
        
        Session::setFlash('success', 'Consultation completed successfully.');
        redirect('/consultations/show/' . $id);
    }
}