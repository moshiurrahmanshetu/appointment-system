<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Session;
use App\Models\Queue;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Consultation;

class DashboardController extends Controller
{
    public function index()
    {
        // Check permission
        if (!can('dashboard.view')) {
            abort(403, 'You do not have permission to access the dashboard.');
        }
        
        $user = Session::get('user');
        
        // Get queue statistics
        $queueModel = new Queue();
        $patientModel = new Patient();
        $appointmentModel = new Appointment();
        $consultationModel = new Consultation();
        
        // Doctor-specific stats
        $doctorId = null;
        if ($user['role_id'] == 4) { // Assuming Doctor has role_id 4
            $doctorId = $user['id'];
        }
        
        $queueStats = $queueModel->getQueueStats($doctorId);
        $patientCount = $patientModel->count();
        $appointmentCount = $appointmentModel->count();
        $consultationStats = $consultationModel->getConsultationStats($doctorId);
        
        $data = [
            'title' => 'Dashboard - ' . config('name'),
            'user' => $user,
            'queueStats' => $queueStats,
            'patientCount' => $patientCount,
            'appointmentCount' => $appointmentCount,
            'consultationStats' => $consultationStats,
            'csrf_token' => Csrf::getToken()
        ];
        
        $this->view('dashboard/index', $data);
    }
}
