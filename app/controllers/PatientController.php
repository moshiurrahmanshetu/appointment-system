<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Session;
use App\Models\AuditLog;
use App\Models\Patient;
use App\Models\User;
use App\Models\Role;

class PatientController extends Controller
{
    private $patientModel;
    private $userModel;
    private $auditModel;
    private $db;
    
    public function __construct()
    {
        parent::__construct();
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
        if (!can('patients.view')) {
            abort(403, 'You do not have permission to view patients.');
        }
        
        $search = $this->input('search', '');
        $filters = [
            'status' => $this->input('status')
        ];
        
        if (!empty($search) || !empty(array_filter($filters))) {
            $patients = $this->patientModel->searchPatients($search, $filters);
        } else {
            $patients = $this->patientModel->getAllPatients();
        }
        
        $data = [
            'title' => 'Patients - ' . config('name'),
            'patients' => $patients,
            'search' => $search,
            'filters' => $filters,
            'csrf_token' => Csrf::getToken()
        ];
        
        $this->view('patients/index', $data);
    }
    
    public function create()
    {
        // Check permission
        if (!can('patients.create')) {
            abort(403, 'You do not have permission to create patients.');
        }
        
        // Get Patient role
        $patientRole = $this->db->fetch("SELECT * FROM roles WHERE slug = 'patient'");
        
        $data = [
            'title' => 'Create Patient - ' . config('name'),
            'patient_role' => $patientRole,
            'csrf_token' => Csrf::getToken()
        ];
        
        $this->view('patients/create', $data);
    }
    
    public function store()
    {
        // Check permission
        if (!can('patients.create')) {
            abort(403, 'User does not have permission to create patients.');
        }
        
        // Validate CSRF token
        if (!Csrf::checkRequest()) {
            Session::setFlash('error', 'Invalid request. Please try again.');
            back();
        }
        
        $currentUser = Session::get('user');
        
        // Validate input
        $errors = $this->validate([
            'full_name' => 'required',
            'phone' => 'required',
            'gender' => 'required',
            'dob' => 'required'
        ]);
        
        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            set_old($this->only(['full_name', 'phone', 'gender', 'dob', 'blood_group', 'address', 'emergency_contact', 'emergency_phone']));
            back();
        }
        
        // Check phone uniqueness
        $phone = $this->input('phone');
        if (!$this->patientModel->checkPhoneAvailability($phone)) {
            Session::setFlash('error', 'Phone number is already in use by another patient.');
            set_old($this->only(['full_name', 'phone', 'gender', 'dob', 'blood_group', 'address', 'emergency_contact', 'emergency_phone']));
            back();
        }
        
        // Generate patient code
        $patientCode = $this->patientModel->generatePatientCode();
        
        // Generate patient user ID for login account
        $patientUserId = $this->patientModel->generatePatientUserId();
        
        // Hash password (use phone number as default password)
        $password = password_hash($phone, PASSWORD_DEFAULT);
        
        // Get Patient role
        $patientRole = $this->db->fetch("SELECT * FROM roles WHERE slug = 'patient'");
        
        // Handle photo upload
        $photoPath = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $photoPath = $this->uploadPhoto($_FILES['photo']);
        }
        
        // Create patient record
        $patientData = [
            'patient_code' => $patientCode,
            'full_name' => $this->input('full_name'),
            'phone' => $phone,
            'gender' => $this->input('gender'),
            'dob' => $this->input('dob'),
            'blood_group' => $this->input('blood_group'),
            'address' => $this->input('address'),
            'emergency_contact' => $this->input('emergency_contact'),
            'emergency_phone' => $this->input('emergency_phone'),
            'photo' => $photoPath,
            'status' => $this->input('status', 'active'),
            'created_by' => $currentUser['id']
        ];
        
        $patientId = $this->patientModel->create($patientData);
        
        // Create linked user account
        $userData = [
            'user_id' => $patientUserId,
            'password' => $password,
            'role_id' => $patientRole['id'],
            'full_name' => $this->input('full_name'),
            'phone' => $phone,
            'status' => $this->input('status', 'active'),
            'created_by' => $currentUser['id']
        ];
        
        $userId = $this->userModel->create($userData);
        
        // Link user account to patient
        $this->patientModel->linkUserAccount($patientId, $userId);
        
        // Log audit
        $this->auditModel->log(
            $currentUser['id'],
            'patient_created',
            'Patient',
            $patientId,
            null,
            array_merge($patientData, ['user_id' => $userId, 'login_id' => $patientUserId])
        );
        
        Session::setFlash('success', 'Patient created successfully. Login ID: ' . $patientUserId . ', Password: ' . $phone);
        redirect('/patients');
    }
    
    public function show($id)
    {
        // Check permission
        if (!can('patients.view')) {
            abort(403, 'You do not have permission to view patients.');
        }
        
        $patient = $this->patientModel->find($id);
        if (!$patient || $patient['deleted_at']) {
            abort(404, 'Patient not found.');
        }
        
        $createdBy = $patient['created_by'] ? $this->userModel->find($patient['created_by']) : null;
        
        $data = [
            'title' => 'Patient Profile - ' . config('name'),
            'patient' => $patient,
            'created_by' => $createdBy,
            'csrf_token' => Csrf::getToken()
        ];
        
        $this->view('patients/show', $data);
    }
    
    public function edit($id)
    {
        // Check permission
        if (!can('patients.edit')) {
            abort(403, 'You do not have permission to edit patients.');
        }
        
        $patient = $this->patientModel->find($id);
        if (!$patient || $patient['deleted_at']) {
            abort(404, 'Patient not found.');
        }
        
        $data = [
            'title' => 'Edit Patient - ' . config('name'),
            'patient' => $patient,
            'csrf_token' => Csrf::getToken()
        ];
        
        $this->view('patients/edit', $data);
    }
    
    public function update($id)
    {
        // Check permission
        if (!can('patients.edit')) {
            abort(403, 'User does not have permission to edit patients.');
        }
        
        // Validate CSRF token
        if (!Csrf::checkRequest()) {
            Session::setFlash('error', 'Invalid request. Please try again.');
            back();
        }
        
        $currentUser = Session::get('user');
        
        $patient = $this->patientModel->find($id);
        if (!$patient || $patient['deleted_at']) {
            abort(404, 'Patient not found.');
        }
        
        // Validate input
        $errors = $this->validate([
            'full_name' => 'required',
            'phone' => 'required',
            'gender' => 'required',
            'dob' => 'required'
        ]);
        
        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            set_old($this->only(['full_name', 'phone', 'gender', 'dob', 'blood_group', 'address', 'emergency_contact', 'emergency_phone', 'status']));
            back();
        }
        
        // Check phone uniqueness
        $phone = $this->input('phone');
        if (!$this->patientModel->checkPhoneAvailability($phone, $id)) {
            Session::setFlash('error', 'Phone number is already in use by another patient.');
            set_old($this->only(['full_name', 'phone', 'gender', 'dob', 'blood_group', 'address', 'emergency_contact', 'emergency_phone', 'status']));
            back();
        }
        
        // Handle photo upload
        $photoPath = $patient['photo'];
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $newPhotoPath = $this->uploadPhoto($_FILES['photo']);
            if ($newPhotoPath) {
                // Delete old photo if exists
                if ($photoPath && file_exists(__DIR__ . '/../../public/assets/' . $photoPath)) {
                    unlink(__DIR__ . '/../../public/assets/' . $photoPath);
                }
                $photoPath = $newPhotoPath;
            }
        }
        
        // Store old values for audit
        $oldValues = [
            'full_name' => $patient['full_name'],
            'phone' => $patient['phone'],
            'gender' => $patient['gender'],
            'dob' => $patient['dob'],
            'blood_group' => $patient['blood_group'],
            'address' => $patient['address'],
            'emergency_contact' => $patient['emergency_contact'],
            'emergency_phone' => $patient['emergency_phone'],
            'status' => $patient['status'],
            'photo' => $patient['photo']
        ];
        
        // Update patient
        $patientData = [
            'full_name' => $this->input('full_name'),
            'phone' => $phone,
            'gender' => $this->input('gender'),
            'dob' => $this->input('dob'),
            'blood_group' => $this->input('blood_group'),
            'address' => $this->input('address'),
            'emergency_contact' => $this->input('emergency_contact'),
            'emergency_phone' => $this->input('emergency_phone'),
            'status' => $this->input('status', $patient['status']),
            'photo' => $photoPath
        ];
        
        $this->patientModel->update($id, $patientData);
        
        // Update linked user account name and phone
        if ($patient['user_id']) {
            $this->userModel->update($patient['user_id'], [
                'full_name' => $this->input('full_name'),
                'phone' => $phone,
                'status' => $this->input('status', $patient['status'])
            ]);
        }
        
        // Log audit
        $this->auditModel->log(
            $currentUser['id'],
            'patient_updated',
            'Patient',
            $id,
            $oldValues,
            $patientData
        );
        
        Session::setFlash('success', 'Patient updated successfully.');
        redirect('/patients');
    }
    
    public function delete($id)
    {
        // Check permission
        if (!can('patients.delete')) {
            abort(403, 'You do not have permission to delete patients.');
        }
        
        $patient = $this->patientModel->find($id);
        if (!$patient || $patient['deleted_at']) {
            abort(404, 'Patient not found.');
        }
        
        // Soft delete patient (also disables linked account)
        $this->patientModel->softDelete($id);
        
        // Log audit
        $this->auditModel->log(
            Session::get('user')['id'],
            'patient_deleted',
            'Patient',
            $id,
            $patient,
            ['deleted_at' => date('Y-m-d H:i:s')]
        );
        
        Session::setFlash('success', 'Patient deleted successfully.');
        redirect('/patients');
    }
    
    public function restore($id)
    {
        // Check permission
        if (!can('patients.restore')) {
            abort(403, 'You do not have permission to restore patients.');
        }
        
        $patient = $this->patientModel->find($id);
        if (!$patient || !$patient['deleted_at']) {
            abort(404, 'Patient not found or not deleted.');
        }
        
        // Restore patient (also enables linked account)
        $this->patientModel->restore($id);
        
        // Log audit
        $this->auditModel->log(
            Session::get('user')['id'],
            'patient_restored',
            'Patient',
            $id,
            ['deleted_at' => $patient['deleted_at']],
            ['deleted_at' => null]
        );
        
        Session::setFlash('success', 'Patient restored successfully.');
        redirect('/patients');
    }
    
    public function updateStatus($id)
    {
        // Check permission
        if (!can('patients.status')) {
            abort(403, 'You do not have permission to change patient status.');
        }
        
        $patient = $this->patientModel->find($id);
        if (!$patient || $patient['deleted_at']) {
            abort(404, 'Patient not found.');
        }
        
        $status = $this->input('status');
        if (!in_array($status, ['active', 'inactive', 'blocked'])) {
            Session::setFlash('error', 'Invalid status.');
            back();
        }
        
        // Update status (also updates linked account)
        $this->patientModel->updateStatus($id, $status);
        
        // Log audit
        $this->auditModel->log(
            Session::get('user')['id'],
            'patient_status_changed',
            'Patient',
            $id,
            ['status' => $patient['status']],
            ['status' => $status]
        );
        
        Session::setFlash('success', 'Patient status updated successfully.');
        redirect('/patients');
    }
    
    public function slip($id)
    {
        // Check permission
        if (!can('patients.view')) {
            abort(403, 'You do not have permission to view patients.');
        }
        
        $patient = $this->patientModel->find($id);
        if (!$patient || $patient['deleted_at']) {
            abort(404, 'Patient not found.');
        }
        
        // Get linked user account
        $userAccount = null;
        if ($patient['user_id']) {
            $userAccount = $this->userModel->find($patient['user_id']);
        }
        
        $data = [
            'title' => 'Patient Registration Slip - ' . config('name'),
            'patient' => $patient,
            'user_account' => $userAccount,
            'clinic_name' => config('name')
        ];
        
        $this->view('patients/slip', $data);
    }
    
    private function uploadPhoto($file)
    {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        
        // Validate file type
        if (!in_array($file['type'], $allowedTypes)) {
            Session::setFlash('error', 'Invalid file type. Only JPG, JPEG, and PNG files are allowed.');
            return null;
        }
        
        // Validate file size
        if ($file['size'] > $maxSize) {
            Session::setFlash('error', 'File size exceeds 2MB limit.');
            return null;
        }
        
        // Create patients directory if it doesn't exist
        $uploadDir = __DIR__ . '/../../public/assets/images/patients/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        
        $uploadPath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return 'images/patients/' . $filename;
        }
        
        return null;
    }
}