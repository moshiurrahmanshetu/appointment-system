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
            <li class="breadcrumb-item active" aria-current="page">Consultations</li>
        </ol>
    </nav>
    <h1 class="page-title">Consultations</h1>
    <p class="text-muted">Manage patient consultations and medical records</p>
</div>

<!-- Dashboard Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-info">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-clipboard2-pulse fs-2 text-info"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0">Draft</h5>
                        <h3 class="mb-0"><?= $stats['draft'] ?? 0 ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-check-circle fs-2 text-success"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0">Completed</h5>
                        <h3 class="mb-0"><?= $stats['completed'] ?? 0 ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-calendar-check fs-2 text-primary"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0">Today</h5>
                        <h3 class="mb-0"><?= $stats['today_total'] ?? 0 ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-warning">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-arrow-repeat fs-2 text-warning"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0">Follow-up Today</h5>
                        <h3 class="mb-0"><?= $stats['follow_up_today'] ?? 0 ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="card-title mb-0">Consultation List</h5>
            </div>
            <div class="col-md-6 text-md-end">
                <?php if (can('consultation.create')): ?>
                <a href="<?= url('consultations/create') ?>" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>New Consultation
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Search and Filters -->
        <form method="GET" action="<?= url('consultations') ?>" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" 
                               class="form-control" 
                               name="search" 
                               placeholder="Search by Consultation No, Patient, Doctor..." 
                               value="<?= e($search) ?>">
                    </div>
                </div>
                <?php if ($doctors): ?>
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
                <?php endif; ?>
                <div class="col-md-2">
                    <select class="form-select" name="consultation_status">
                        <option value="">All Status</option>
                        <option value="Draft" <?= $filters['consultation_status'] === 'Draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="Completed" <?= $filters['consultation_status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="visit_type">
                        <option value="">All Types</option>
                        <option value="New" <?= $filters['visit_type'] === 'New' ? 'selected' : '' ?>>New</option>
                        <option value="Follow-up" <?= $filters['visit_type'] === 'Follow-up' ? 'selected' : '' ?>>Follow-up</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-filter me-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>

        <!-- Consultations Table -->
        <?php if (!empty($consultations)): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Consultation No</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Date</th>
                        <th>Visit Type</th>
                        <th>Status</th>
                        <th>Follow-up</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($consultations as $consultation): ?>
                    <tr>
                        <td>
                            <code><?= e($consultation['consultation_no']) ?></code>
                        </td>
                        <td>
                            <div>
                                <div class="fw-bold"><?= e($consultation['patient_name']) ?></div>
                                <div class="text-muted small"><?= e($consultation['patient_code']) ?></div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold"><?= e($consultation['doctor_name']) ?></div>
                        </td>
                        <td>
                            <div><?= date('M j, Y', strtotime($consultation['created_at'])) ?></div>
                            <div class="text-muted small"><?= date('g:i A', strtotime($consultation['created_at'])) ?></div>
                        </td>
                        <td>
                            <span class="badge bg-secondary"><?= e($consultation['visit_type']) ?></span>
                        </td>
                        <td>
                            <?php
                            $statusClass = $consultation['consultation_status'] === 'Completed' ? 'bg-success' : 'bg-warning';
                            ?>
                            <span class="badge <?= $statusClass ?>"><?= e($consultation['consultation_status']) ?></span>
                        </td>
                        <td>
                            <?php if ($consultation['follow_up_required'] === 'Yes' && $consultation['follow_up_date']): ?>
                            <span class="badge bg-info"><?= date('M j, Y', strtotime($consultation['follow_up_date'])) ?></span>
                            <?php else: ?>
                            <span class="text-muted">No</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <?php if (can('consultation.view')): ?>
                                <a href="<?= url('consultations/show/' . $consultation['id']) ?>" class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (can('consultation.edit')): ?>
                                <a href="<?= url('consultations/edit/' . $consultation['id']) ?>" class="btn btn-sm btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (can('consultation.complete') && $consultation['consultation_status'] === 'Draft'): ?>
                                <a href="<?= url('consultations/complete/' . $consultation['id']) ?>" class="btn btn-sm btn-outline-success" title="Complete" onclick="return confirm('Complete this consultation?')">
                                    <i class="bi bi-check-circle"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (can('consultation.delete')): ?>
                                <a href="<?= url('consultations/delete/' . $consultation['id']) ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this consultation?')">
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
            <i class="bi bi-clipboard2-pulse fs-1 text-muted mb-3"></i>
            <p class="text-muted">No consultations found.</p>
            <?php if (can('consultation.create')): ?>
            <a href="<?= url('consultations/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Create First Consultation
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>