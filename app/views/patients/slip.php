<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Registration Slip - <?= e($clinic_name) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f0f0;
            padding: 20px;
        }
        
        .slip {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .slip-header {
            text-align: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .slip-header h1 {
            color: #007bff;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .slip-header p {
            color: #666;
            font-size: 14px;
        }
        
        .slip-title {
            text-align: center;
            background: #007bff;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
            font-size: 22px;
            font-weight: bold;
        }
        
        .patient-info {
            margin-bottom: 30px;
        }
        
        .patient-info h2 {
            color: #333;
            font-size: 20px;
            margin-bottom: 15px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .info-label {
            flex: 1;
            font-weight: bold;
            color: #555;
        }
        
        .info-value {
            flex: 2;
            color: #333;
        }
        
        .info-value .patient-code {
            font-family: 'Courier New', monospace;
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }
        
        .info-value .login-id {
            font-family: 'Courier New', monospace;
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
        }
        
        .info-value .password {
            font-family: 'Courier New', monospace;
            font-size: 18px;
            font-weight: bold;
            color: #dc3545;
        }
        
        .login-credentials {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .login-credentials h2 {
            color: #856404;
            font-size: 20px;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .qr-placeholder {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            border: 2px dashed #ccc;
            border-radius: 10px;
            background: #f8f9fa;
        }
        
        .qr-placeholder .qr-code {
            width: 150px;
            height: 150px;
            background: white;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #ddd;
        }
        
        .qr-placeholder p {
            color: #666;
            margin-top: 10px;
            font-size: 14px;
        }
        
        .slip-footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }
        
        .slip-footer p {
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .print-btn:hover {
            background: #0056b3;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .print-btn {
                display: none;
            }
            
            .slip {
                box-shadow: none;
                border-radius: 0;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">
        <i class="bi bi-printer"></i> Print Slip
    </button>
    
    <div class="slip">
        <div class="slip-header">
            <h1><?= e($clinic_name) ?></h1>
            <p>Patient Registration Confirmation</p>
        </div>
        
        <div class="slip-title">
            PATIENT REGISTRATION SLIP
        </div>
        
        <div class="patient-info">
            <h2>Patient Information</h2>
            
            <div class="info-row">
                <div class="info-label">Patient Code:</div>
                <div class="info-value">
                    <span class="patient-code"><?= e($patient['patient_code']) ?></span>
                </div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Patient Name:</div>
                <div class="info-value"><?= e($patient['full_name']) ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Phone Number:</div>
                <div class="info-value"><?= e($patient['phone']) ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Date of Birth:</div>
                <div class="info-value"><?= date('F j, Y', strtotime($patient['dob'])) ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Gender:</div>
                <div class="info-value"><?= ucfirst($patient['gender']) ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Blood Group:</div>
                <div class="info-value"><?= $patient['blood_group'] ? e($patient['blood_group']) : 'N/A' ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Registration Date:</div>
                <div class="info-value"><?= date('F j, Y', strtotime($patient['created_at'])) ?></div>
            </div>
        </div>
        
        <?php if ($user_account): ?>
        <div class="login-credentials">
            <h2>Login Credentials</h2>
            
            <div class="info-row">
                <div class="info-label">Login User ID:</div>
                <div class="info-value">
                    <span class="login-id"><?= e($user_account['user_id']) ?></span>
                </div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Default Password:</div>
                <div class="info-value">
                    <span class="password"><?= e($patient['phone']) ?></span>
                </div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Account Status:</div>
                <div class="info-value">
                    <?php
                    $statusClass = match($user_account['status']) {
                        'active' => 'color: #28a745;',
                        'inactive' => 'color: #ffc107;',
                        'blocked' => 'color: #dc3545;',
                        default => 'color: #6c757d;'
                    };
                    ?>
                    <span style="<?= $statusClass ?> font-weight: bold;"><?= ucfirst($user_account['status']) ?></span>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="qr-placeholder">
            <div class="qr-code">
                <svg width="100" height="100" viewBox="0 0 100 100">
                    <rect x="10" y="10" width="20" height="20" fill="#000"/>
                    <rect x="70" y="10" width="20" height="20" fill="#000"/>
                    <rect x="10" y="70" width="20" height="20" fill="#000"/>
                    <rect x="40" y="40" width="20" height="20" fill="#000"/>
                    <rect x="35" y="10" width="5" height="5" fill="#000"/>
                    <rect x="50" y="15" width="5" height="5" fill="#000"/>
                    <rect x="15" y="40" width="5" height="5" fill="#000"/>
                    <rect x="75" y="45" width="5" height="5" fill="#000"/>
                    <rect x="45" y="75" width="5" height="5" fill="#000"/>
                    <rect x="60" y="80" width="5" height="5" fill="#000"/>
                </svg>
            </div>
            <p>Scan QR Code for Quick Access</p>
        </div>
        
        <div class="slip-footer">
            <p><strong>Important:</strong> Please keep this slip safe. Your login credentials are required to access the patient portal.</p>
            <p><strong>Note:</strong> You can change your password after first login.</p>
            <p style="margin-top: 20px; font-size: 12px;">Generated on: <?= date('F j, Y g:i A') ?></p>
        </div>
    </div>
    
    <script>
        // Auto print on load (optional - remove if not desired)
        // window.onload = function() {
        //     window.print();
        // };
    </script>
</body>
</html>
