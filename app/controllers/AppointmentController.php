<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Session;
use App\Models\AuditLog;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use App\Models\Queue;

class AppointmentController extends Controller
{
    private $appointmentModel;
    private $patientModel;
    private $userModel;
    private $auditModel;
    protected $db;
    
    public function __construct()
    {
        parent::__construct();
        $this->appointmentModel = new Appointment();
        $this->patientModel = new Patient();
        $this->userModel = new User();
        $this->auditModel = new AuditLog();
        $this->db = \App\Core\Database::getInstance();
    }
    
    public function index($id = null)
    {
        // If ID is provided, redirect to show method
        if ($id) {
            return $this->show($id);
        }
        
        // Check permission
        if (!can('appointments.view')) {
            abort(403, 'You do not have permission to view appointments.');
        }
        
        $search = $this->input('search', '');
        $filters = [
            'doctor_id' => $this->input('doctor_id'),
            'appointment_date' => $this->input('appointment_date'),
            'status' => $this->input('status'),
            'priority' => $this->input('priority')
        ];
        
        if (!empty($search) || !empty(array_filter($filters))) {
            $appointments = $this->appointmentModel->searchAppointments($search, $filters);
        } else {
            $appointments = $this->appointmentModel->getAllAppointments();
        }
        
        // Get doctors for filter dropdown
        $doctors = $this->db->fetchAll("SELECT u.* FROM users u 
                                          INNER JOIN role_permissions rp ON rp.role_id = u.role_id
                                          INNER JOIN permissions p ON p.id = rp.permission_id
                                          WHERE p.slug = 'appointments.create'
                                          AND u.deleted_at IS NULL
                                          GROUP BY u.id
                                          ORDER BY u.full_name ASC");
        
        $data = [
            'title' => 'Appointments - ' . config('name'),
            'appointments' => $appointments,
            'doctors' => $doctors,
            'search' => $search,
            'filters' => $filters,
            'csrf_token' => Csrf::getToken()
        ];
        
        $this->view('appointments/index', $data);
    }
    
    public function create()
    {
        // Check permission
        if (!can('appointments.create')) {
            abort(403, 'You do not have permission to create appointments.');
        }
        
        // Get patients and doctors
        $patients = $this->patientModel->getAllPatients();
        $doctors = $this->db->fetchAll("SELECT u.* FROM users u 
                                          INNER JOIN role_permissions rp ON rp.role_id = u.role_id
                                          INNER JOIN permissions p ON p.id = rp.permission_id
                                          WHERE p.slug = 'appointments.create'
                                          AND u.deleted_at IS NULL
                                          GROUP BY u.id
                                          ORDER BY u.full_name ASC");
        
        $data = [
            'title' => 'Create Appointment - ' . config('name'),
            'patients' => $patients,
            'doctors' => $doctors,
            'csrf_token' => Csrf::getToken()
        ];
        
        $this->view('appointments/create', $data);
    }
    
    public function store()
    {
        // Check permission
        if (!can('appointments.create')) {
            abort(403, 'User does not have permission to create appointments.');
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
            'doctor_id' => 'required',
            'appointment_date' => 'required',
            'appointment_time' => 'required'
        ]);
        
        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            set_old($this->only(['patient_id', 'doctor_id', 'appointment_date', 'appointment_time', 'visit_type', 'priority', 'remarks']));
            back();
        }
        
        // Validate patient exists
        $patientId = $this->input('patient_id');
        $patient = $this->patientModel->find($patientId);
        if (!$patient || $patient['deleted_at']) {
            Session::setFlash('error', 'Invalid patient selected.');
            set_old($this->only(['patient_id', 'doctor_id', 'appointment_date', 'appointment_time', 'visit_type', 'priority', 'remarks']));
            back();
        }
        
        // Validate doctor exists
        $doctorId = $this->input('doctor_id');
        $doctor = $this->userModel->find($doctorId);
        if (!$doctor || $doctor['deleted_at']) {
            Session::setFlash('error', 'Invalid doctor selected.');
            set_old($this->only(['patient_id', 'doctor_id', 'appointment_date', 'appointment_time', 'visit_type', 'priority', 'remarks']));
            back();
        }
        
        // Check appointment availability
        $appointmentDate = $this->input('appointment_date');
        $appointmentTime = $this->input('appointment_time');
        if (!$this->appointmentModel->checkAppointmentAvailability($doctorId, $appointmentDate, $appointmentTime)) {
            Session::setFlash('error', 'Doctor is already booked at this time. Please select a different time.');
            set_old($this->only(['patient_id', 'doctor_id', 'appointment_date', 'appointment_time', 'visit_type', 'priority', 'remarks']));
            back();
        }
        
        // Generate appointment number
        $appointmentNo = $this->appointmentModel->generateAppointmentNo();
        
        // Generate serial number for this doctor on this date
        $serialNo = $this->appointmentModel->generateSerialNo($doctorId, $appointmentDate);
        
        // Create appointment
        $appointmentData = [
            'appointment_no' => $appointmentNo,
            'patient_id' => $patientId,
            'doctor_id' => $doctorId,
            'appointment_date' => $appointmentDate,
            'appointment_time' => $appointmentTime,
            'serial_no' => $serialNo,
            'visit_type' => $this->input('visit_type', 'New'),
            'priority' => $this->input('priority', 'Normal'),
            'status' => 'Pending',
            'remarks' => $this->input('remarks'),
            'created_by' => $currentUser['id']
        ];
        
        $appointmentId = $this->appointmentModel->create($appointmentData);
        
        // Log audit
        $this->auditModel->log(
            $currentUser['id'],
            'appointment_created',
            'Appointment',
            $appointmentId,
            null,
            $appointmentData
        );
        
        Session::setFlash('success', 'Appointment created successfully. Appointment No: ' . $appointmentNo . ', Serial: ' . $serialNo);
        redirect('/appointments');
    }
    
    public function show($id)
    {
        // Check permission
        if (!can('appointments.view')) {
            abort(403, 'You do not have permission to view appointments.');
        }
        
        $appointment = $this->appointmentModel->getAppointmentWithDetails($id);
        if (!$appointment || $appointment['deleted_at']) {
            abort(404, 'Appointment not found.');
        }
        
        $data = [
            'title' => 'Appointment Details - ' . config('name'),
            'appointment' => $appointment,
            'csrf_token' => Csrf::getToken()
        ];
        
        $this->view('appointments/show', $data);
    }
    
    public function edit($id)
    {
        // Check permission
        if (!can('appointments.edit')) {
            abort(403, 'You do not have permission to edit appointments.');
        }
        
        $appointment = $this->appointmentModel->getAppointmentWithDetails($id);
        if (!$appointment || $appointment['deleted_at']) {
            abort(404, 'Appointment not found.');
        }
        
        // Get patients and doctors
        $patients = $this->patientModel->getAllPatients();
        $doctors = $this->db->fetchAll("SELECT u.* FROM users u 
                                          INNER JOIN role_permissions rp ON rp.role_id = u.role_id
                                          INNER JOIN permissions p ON p.id = rp.permission_id
                                          WHERE p.slug = 'appointments.create'
                                          AND u.deleted_at IS NULL
                                          GROUP BY u.id
                                          ORDER BY u.full_name ASC");
        
        $data = [
            'title' => 'Edit Appointment - ' . config('name'),
            'appointment' => $appointment,
            'patients' => $patients,
            'doctors' => $doctors,
            'csrf_token' => Csrf::getToken()
        ];
        
        $this->view('appointments/edit', $data);
    }
    
    public function update($id)
    {
        // Check permission
        if (!can('appointments.edit')) {
            abort(403, 'User does not have permission to edit appointments.');
        }
        
        // Validate CSRF token
        if (!Csrf::checkRequest()) {
            Session::setFlash('error', 'Invalid request. Please try again.');
            back();
        }
        
        $currentUser = Session::get('user');
        
        $appointment = $this->appointmentModel->getAppointmentWithDetails($id);
        if (!$appointment || $appointment['deleted_at']) {
            abort(404, 'Appointment not found.');
        }
        
        // Validate input
        $errors = $this->validate([
            'patient_id' => 'required',
            'doctor_id' => 'required',
            'appointment_date' => 'required',
            'appointment_time' => 'required'
        ]);
        
        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            set_old($this->only(['patient_id', 'doctor_id', 'appointment_date', 'appointment_time', 'visit_type', 'priority', 'remarks', 'status']));
            back();
        }
        
        // Validate patient exists
        $patientId = $this->input('patient_id');
        $patient = $this->patientModel->find($patientId);
        if (!$patient || $patient['deleted_at']) {
            Session::setFlash('error', 'Invalid patient selected.');
            set_old($this->only(['patient_id', 'doctor_id', 'appointment_date', 'appointment_time', 'visit_type', 'priority', 'remarks', 'status']));
            back();
        }
        
        // Validate doctor exists
        $doctorId = $this->input('doctor_id');
        $doctor = $this->userModel->find($doctorId);
        if (!$doctor || $doctor['deleted_at']) {
            Session::setFlash('error', 'Invalid doctor selected.');
            set_old($this->only(['patient_id', 'doctor_id', 'appointment_date', 'appointment_time', 'visit_type', 'priority', 'remarks', 'status']));
            back();
        }
        
        // Check appointment availability (exclude current appointment)
        $appointmentDate = $this->input('appointment_date');
        $appointmentTime = $this->input('appointment_time');
        if (!$this->appointmentModel->checkAppointmentAvailability($doctorId, $appointmentDate, $appointmentTime, $id)) {
            Session::setFlash('error', 'Doctor is already booked at this time. Please select a different time.');
            set_old($this->only(['patient_id', 'doctor_id', 'appointment_date', 'appointment_time', 'visit_type', 'priority', 'remarks', 'status']));
            back();
        }
        
        // Store old values for audit
        $oldValues = [
            'patient_id' => $appointment['patient_id'],
            'doctor_id' => $appointment['doctor_id'],
            'appointment_date' => $appointment['appointment_date'],
            'appointment_time' => $appointment['appointment_time'],
            'visit_type' => $appointment['visit_type'],
            'priority' => $appointment['priority'],
            'status' => $appointment['status'],
            'remarks' => $appointment['remarks']
        ];
        
        // If doctor or date changed, regenerate serial number
        $serialNo = $appointment['serial_no'];
        if ($doctorId != $appointment['doctor_id'] || $appointmentDate != $appointment['appointment_date']) {
            $serialNo = $this->appointmentModel->generateSerialNo($doctorId, $appointmentDate);
        }
        
        // Update appointment
        $appointmentData = [
            'patient_id' => $patientId,
            'doctor_id' => $doctorId,
            'appointment_date' => $appointmentDate,
            'appointment_time' => $appointmentTime,
            'serial_no' => $serialNo,
            'visit_type' => $this->input('visit_type', 'New'),
            'priority' => $this->input('priority', 'Normal'),
            'status' => $this->input('status', $appointment['status']),
            'remarks' => $this->input('remarks'),
            'updated_by' => $currentUser['id']
        ];
        
        $this->appointmentModel->update($id, $appointmentData);
        
        // Log audit
        $this->auditModel->log(
            $currentUser['id'],
            'appointment_updated',
            'Appointment',
            $id,
            $oldValues,
            $appointmentData
        );
        
        Session::setFlash('success', 'Appointment updated successfully.');
        redirect('/appointments');
    }
    
    public function delete($id)
    {
        // Check permission
        if (!can('appointments.delete')) {
            abort(403, 'You do not have permission to delete appointments.');
        }
        
        $appointment = $this->appointmentModel->getAppointmentWithDetails($id);
        if (!$appointment || $appointment['deleted_at']) {
            abort(404, 'Appointment not found.');
        }
        
        // Soft delete appointment
        $this->appointmentModel->softDelete($id);
        
        // Log audit
        $this->auditModel->log(
            Session::get('user')['id'],
            'appointment_deleted',
            'Appointment',
            $id,
            $appointment,
            ['deleted_at' => date('Y-m-d H:i:s')]
        );
        
        Session::setFlash('success', 'Appointment deleted successfully.');
        redirect('/appointments');
    }
    
    public function restore($id)
    {
        // Check permission
        if (!can('appointments.restore')) {
            abort(403, 'You do not have permission to restore appointments.');
        }
        
        $appointment = $this->appointmentModel->getAppointmentWithDetails($id);
        if (!$appointment || !$appointment['deleted_at']) {
            abort(404, 'Appointment not found or not deleted.');
        }
        
        // Restore appointment
        $this->appointmentModel->restore($id);
        
        // Log audit
        $this->auditModel->log(
            Session::get('user')['id'],
            'appointment_restored',
            'Appointment',
            $id,
            ['deleted_at' => $appointment['deleted_at']],
            ['deleted_at' => null]
        );
        
        Session::setFlash('success', 'Appointment restored successfully.');
        redirect('/appointments');
    }
    
    public function updateStatus($id)
    {
        // Check permission
        if (!can('appointments.edit')) {
            abort(403, 'You do not have permission to change appointment status.');
        }
        
        $appointment = $this->appointmentModel->getAppointmentWithDetails($id);
        if (!$appointment || $appointment['deleted_at']) {
            abort(404, 'Appointment not found.');
        }
        
        $status = $this->input('status');
        $validStatuses = ['Pending', 'Confirmed', 'Checked In', 'In Queue', 'With Doctor', 'Completed', 'Cancelled'];
        if (!in_array($status, $validStatuses)) {
            Session::setFlash('error', 'Invalid status.');
            back();
        }
        
        // Update status
        $this->appointmentModel->updateStatus($id, $status);
        
        // Auto-create queue entry when status is "Checked In"
        if ($status === 'Checked In') {
            $queueModel = new \App\Models\Queue();
            $queueId = $queueModel->createFromAppointment($id);
            if ($queueId) {
                // Log queue creation
                $this->auditModel->log(
                    Session::get('user')['id'],
                    'queue_created',
                    'Queue',
                    $queueId,
                    null,
                    ['appointment_id' => $id, 'queue_status' => 'Waiting']
                );
            }
        }
        
        // Log audit
        $this->auditModel->log(
            Session::get('user')['id'],
            'appointment_status_changed',
            'Appointment',
            $id,
            ['status' => $appointment['status']],
            ['status' => $status]
        );
        
        Session::setFlash('success', 'Appointment status updated successfully.');
        redirect('/appointments');
    }
}