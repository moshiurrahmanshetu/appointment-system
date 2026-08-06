<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Session;
use App\Models\Queue;
use App\Models\Patient;
use App\Models\Appointment;

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
        
        // Doctor-specific queue stats
        $doctorId = null;
        if ($user['role_id'] == 4) { // Assuming Doctor has role_id 4
            $doctorId = $user['id'];
        }
        
        $queueStats = $queueModel->getQueueStats($doctorId);
        $patientCount = $patientModel->count();
        $appointmentCount = $appointmentModel->count();
        
        $data = [
            'title' => 'Dashboard - ' . config('name'),
            'user' => $user,
            'queueStats' => $queueStats,
            'patientCount' => $patientCount,
            'appointmentCount' => $appointmentCount,
            'csrf_token' => Csrf::getToken()
        ];
        
        $this->view('dashboard/index', $data);
    }
}
