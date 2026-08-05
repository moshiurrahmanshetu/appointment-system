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
            <li class="breadcrumb-item active" aria-current="page">Patients</li>
        </ol>
    </nav>
    <h1 class="page-title">Patients</h1>
    <p class="text-muted">Manage patient records and information</p>
</div>

<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="card-title mb-0">Patient List</h5>
            </div>
            <div class="col-md-6 text-md-end">
                <?php if (can('patients.create')): ?>
                <a href="<?= url('patients/create') ?>" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Add Patient
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Search and Filters -->
        <form method="GET" action="<?= url('patients') ?>" class="mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" 
                               class="form-control" 
                               name="search" 
                               placeholder="Search by Patient Code, Name, Phone..." 
                               value="<?= e($search) ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $filters['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        <option value="blocked" <?= $filters['status'] === 'blocked' ? 'selected' : '' ?>>Blocked</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-filter me-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>

        <!-- Patients Table -->
        <?php if (!empty($patients)): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Patient Code</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Account Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($patients as $patient): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="patient-photo me-3">
                                    <?php if ($patient['photo']): ?>
                                    <img src="<?= asset($patient['photo']) ?>" alt="<?= e($patient['full_name']) ?>" class="rounded-circle" width="40" height="40">
                                    <?php else: ?>
                                    <div class="photo-placeholder rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" width="40" height="40">
                                        <?= strtoupper(substr($patient['full_name'], 0, 1)) ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="fw-bold"><?= e($patient['full_name']) ?></div>
                                    <div class="text-muted small"><?= e($patient['phone']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <code><?= e($patient['patient_code']) ?></code>
                        </td>
                        <td>
                            <?= e($patient['phone']) ?>
                        </td>
                        <td>
                            <?php
                            $statusClass = match($patient['status']) {
                                'active' => 'bg-success',
                                'inactive' => 'bg-warning',
                                'blocked' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?= $statusClass ?>"><?= ucfirst($patient['status']) ?></span>
                        </td>
                        <td>
                            <?php if ($patient['account_status']): ?>
                            <?php
                            $accountStatusClass = match($patient['account_status']) {
                                'active' => 'bg-success',
                                'inactive' => 'bg-warning',
                                'blocked' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?= $accountStatusClass ?>"><?= ucfirst($patient['account_status']) ?></span>
                            <?php else: ?>
                            <span class="badge bg-secondary">No Account</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <?php if (can('patients.view')): ?>
                                <a href="<?= url('patients/show/' . $patient['id']) ?>" class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (can('patients.view')): ?>
                                <a href="<?= url('patients/slip/' . $patient['id']) ?>" class="btn btn-sm btn-outline-info" title="Print Slip">
                                    <i class="bi bi-printer"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (can('patients.edit')): ?>
                                <a href="<?= url('patients/edit/' . $patient['id']) ?>" class="btn btn-sm btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (can('patients.status')): ?>
                                <button type="button" class="btn btn-sm btn-outline-info dropdown-toggle" data-bs-toggle="dropdown" title="Status">
                                    <i class="bi bi-gear"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="<?= url('patients/status/' . $patient['id']) ?>?status=active">
                                            <i class="bi bi-check-circle me-2"></i>Active
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?= url('patients/status/' . $patient['id']) ?>?status=inactive">
                                            <i class="bi bi-dash-circle me-2"></i>Inactive
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="<?= url('patients/status/' . $patient['id']) ?>?status=blocked">
                                            <i class="bi bi-x-circle me-2"></i>Blocked
                                        </a>
                                    </li>
                                </ul>
                                <?php endif; ?>
                                
                                <?php if (can('patients.delete')): ?>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deletePatient(<?= $patient['id'] ?>)" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
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
            <i class="bi bi-people text-muted fs-1 d-block mb-3"></i>
            <h5 class="text-muted">No patients found</h5>
            <p class="text-muted">Create your first patient to get started</p>
            <?php if (can('patients.create')): ?>
            <a href="<?= url('patients/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Add Patient
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.patient-photo img {
    object-fit: cover;
}
.photo-placeholder {
    width: 40px;
    height: 40px;
    font-size: 16px;
    font-weight: 600;
}
</style>

<script>
function deletePatient(patientId) {
    if (confirm('Are you sure you want to delete this patient?')) {
        window.location.href = '<?= url('patients/delete') ?>/' + patientId;
    }
}
</script>
