<?php if (has_flash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        <?= e(flash('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (has_flash('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <?= e(flash('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="page-header mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Appointments</li>
        </ol>
    </nav>
    <h1 class="page-title">Appointments</h1>
    <p class="text-muted">Manage appointment scheduling and patient visits</p>
</div>

<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="card-title mb-0">Appointment List</h5>
            </div>
            <div class="col-md-6 text-md-end">
                <?php if (can('appointments.create')): ?>
                <a href="<?= url('appointments/create') ?>" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>New Appointment
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Search and Filters -->
        <form method="GET" action="<?= url('appointments') ?>" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" 
                               class="form-control" 
                               name="search" 
                               placeholder="Search by Appointment No, Patient, Doctor..." 
                               value="<?= e($search) ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="doctor_id">
                        <option value="">All Doctors</option>
                        <?php foreach ($doctors as $doctor): ?>
                        <option value="<?= $doctor['id'] ?>" <?= $filters['doctor_id'] == $doctor['id'] ? 'selected' : '' ?>>
                            <?= e($doctor['full_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" 
                           class="form-control" 
                           name="appointment_date" 
                           value="<?= e($filters['appointment_date']) ?>"
                           placeholder="Filter by Date">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="Pending" <?= $filters['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Confirmed" <?= $filters['status'] === 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
                        <option value="Checked In" <?= $filters['status'] === 'Checked In' ? 'selected' : '' ?>>Checked In</option>
                        <option value="In Queue" <?= $filters['status'] === 'In Queue' ? 'selected' : '' ?>>In Queue</option>
                        <option value="With Doctor" <?= $filters['status'] === 'With Doctor' ? 'selected' : '' ?>>With Doctor</option>
                        <option value="Completed" <?= $filters['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="Cancelled" <?= $filters['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-filter me-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>

        <!-- Appointments Table -->
        <?php if (!empty($appointments)): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Appointment No</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Date & Time</th>
                        <th>Serial</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $appointment): ?>
                    <tr>
                        <td>
                            <code><?= e($appointment['appointment_no']) ?></code>
                        </td>
                        <td>
                            <div>
                                <div class="fw-bold"><?= e($appointment['patient_name']) ?></div>
                                <div class="text-muted small"><?= e($appointment['patient_code']) ?></div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold"><?= e($appointment['doctor_name']) ?></div>
                        </td>
                        <td>
                            <div><?= date('M j, Y', strtotime($appointment['appointment_date'])) ?></div>
                            <div class="text-muted small"><?= date('g:i A', strtotime($appointment['appointment_time'])) ?></div>
                        </td>
                        <td>
                            <span class="badge bg-primary">#<?= $appointment['serial_no'] ?></span>
                        </td>
                        <td>
                            <?php
                            $priorityClass = match($appointment['priority']) {
                                'Normal' => 'bg-secondary',
                                'Urgent' => 'bg-warning',
                                'Emergency' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?= $priorityClass ?>"><?= e($appointment['priority']) ?></span>
                        </td>
                        <td>
                            <?php
                            $statusClass = match($appointment['status']) {
                                'Pending' => 'bg-warning',
                                'Confirmed' => 'bg-info',
                                'Checked In' => 'bg-primary',
                                'In Queue' => 'bg-secondary',
                                'With Doctor' => 'bg-success',
                                'Completed' => 'bg-success',
                                'Cancelled' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?= $statusClass ?>"><?= e($appointment['status']) ?></span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <?php if (can('appointments.view')): ?>
                                <a href="<?= url('appointments/show/' . $appointment['id']) ?>" class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (can('appointments.edit')): ?>
                                <a href="<?= url('appointments/edit/' . $appointment['id']) ?>" class="btn btn-sm btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (can('appointments.edit')): ?>
                                <button type="button" class="btn btn-sm btn-outline-info dropdown-toggle" data-bs-toggle="dropdown" title="Status">
                                    <i class="bi bi-gear"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="<?= url('appointments/status/' . $appointment['id']) ?>?status=Pending">
                                            <i class="bi bi-clock me-2"></i>Pending
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?= url('appointments/status/' . $appointment['id']) ?>?status=Confirmed">
                                            <i class="bi bi-check-circle me-2"></i>Confirmed
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?= url('appointments/status/' . $appointment['id']) ?>?status=Checked In">
                                            <i class="bi bi-person-check me-2"></i>Checked In
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?= url('appointments/status/' . $appointment['id']) ?>?status=In Queue">
                                            <i class="bi bi-list-ol me-2"></i>In Queue
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?= url('appointments/status/' . $appointment['id']) ?>?status=With Doctor">
                                            <i class="bi bi-person-badge me-2"></i>With Doctor
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?= url('appointments/status/' . $appointment['id']) ?>?status=Completed">
                                            <i class="bi bi-check2-circle me-2"></i>Completed
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?= url('appointments/status/' . $appointment['id']) ?>?status=Cancelled">
                                            <i class="bi bi-x-circle me-2"></i>Cancelled
                                        </a>
                                    </li>
                                </ul>
                                </button>
                                <?php endif; ?>
                                
                                <?php if (can('appointments.delete')): ?>
                                <a href="<?= url('appointments/delete/' . $appointment['id']) ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this appointment?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="bi bi-calendar-x fs-1 text-muted mb-3"></i>
            <p class="text-muted">No appointments found.</p>
            <?php if (can('appointments.create')): ?>
            <a href="<?= url('appointments/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Create First Appointment
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>