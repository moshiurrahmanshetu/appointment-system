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
            <li class="breadcrumb-item active" aria-current="page">Users</li>
        </ol>
    </nav>
    <h1 class="page-title">Users</h1>
    <p class="text-muted">Manage system users and their permissions</p>
</div>

<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="card-title mb-0">User List</h5>
            </div>
            <div class="col-md-6 text-md-end">
                <?php if (can('users.create')): ?>
                <a href="<?= url('users/create') ?>" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Create User
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Search and Filters -->
        <form method="GET" action="<?= url('users') ?>" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" 
                               class="form-control" 
                               name="search" 
                               placeholder="Search by User ID, Name, Email, Phone..." 
                               value="<?= e($search) ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="role_id">
                        <option value="">All Roles</option>
                        <?php foreach ($roles as $role): ?>
                        <option value="<?= $role['id'] ?>" <?= $filters['role_id'] == $role['id'] ? 'selected' : '' ?>>
                            <?= e($role['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $filters['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        <option value="blocked" <?= $filters['status'] === 'blocked' ? 'selected' : '' ?>>Blocked</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-filter me-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>

        <!-- Users Table -->
        <?php if (!empty($users)): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>User ID</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="user-avatar me-3">
                                    <?php if ($user['avatar']): ?>
                                    <img src="<?= asset($user['avatar']) ?>" alt="<?= e($user['full_name']) ?>" class="rounded-circle" width="40" height="40">
                                    <?php else: ?>
                                    <div class="avatar-placeholder rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" width="40" height="40">
                                        <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="fw-bold"><?= e($user['full_name']) ?></div>
                                    <div class="text-muted small"><?= e($user['email'] ?? 'No email') ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <code><?= e($user['user_id']) ?></code>
                        </td>
                        <td>
                            <span class="badge bg-secondary"><?= e($user['role_name']) ?></span>
                        </td>
                        <td>
                            <?php
                            $statusClass = match($user['status']) {
                                'active' => 'bg-success',
                                'inactive' => 'bg-warning',
                                'blocked' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?= $statusClass ?>"><?= ucfirst($user['status']) ?></span>
                        </td>
                        <td>
                            <small><?= date('M d, Y', strtotime($user['created_at'])) ?></small>
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <?php if (can('users.view')): ?>
                                <a href="<?= url('users/show/' . $user['id']) ?>" class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (can('users.edit')): ?>
                                <a href="<?= url('users/edit/' . $user['id']) ?>" class="btn btn-sm btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (can('users.status')): ?>
                                <button type="button" class="btn btn-sm btn-outline-info dropdown-toggle" data-bs-toggle="dropdown" title="Status">
                                    <i class="bi bi-gear"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="<?= url('users/status/' . $user['id']) ?>?status=active">
                                            <i class="bi bi-check-circle me-2"></i>Active
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?= url('users/status/' . $user['id']) ?>?status=inactive">
                                            <i class="bi bi-dash-circle me-2"></i>Inactive
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="<?= url('users/status/' . $user['id']) ?>?status=blocked">
                                            <i class="bi bi-x-circle me-2"></i>Blocked
                                        </a>
                                    </li>
                                </ul>
                                <?php endif; ?>
                                
                                <?php if (can('users.delete')): ?>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-confirm="Are you sure you want to delete this user?" onclick="deleteUser(<?= $user['id'] ?>)" title="Delete">
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
            <h5 class="text-muted">No users found</h5>
            <p class="text-muted">Create your first user to get started</p>
            <?php if (can('users.create')): ?>
            <a href="<?= url('users/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Create User
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.user-avatar img {
    object-fit: cover;
}
.avatar-placeholder {
    width: 40px;
    height: 40px;
    font-size: 16px;
    font-weight: 600;
}
</style>

<script>
function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        window.location.href = '<?= url('users/delete') ?>/' + userId;
    }
}
</script>
