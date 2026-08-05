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
            <li class="breadcrumb-item"><a href="<?= url('users') ?>">Users</a></li>
            <li class="breadcrumb-item active" aria-current="page">User Profile</li>
        </ol>
    </nav>
    <h1 class="page-title">User Profile</h1>
    <p class="text-muted">View user details and information</p>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="profile-avatar mb-4">
                    <?php if ($user['avatar']): ?>
                    <img src="<?= asset($user['avatar']) ?>" alt="<?= e($user['full_name']) ?>" class="rounded-circle" width="120" height="120">
                    <?php else: ?>
                    <div class="avatar-placeholder rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto" style="width: 120px; height: 120px; font-size: 48px; font-weight: 600;">
                        <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
                    </div>
                    <?php endif; ?>
                </div>
                <h4 class="mb-1"><?= e($user['full_name']) ?></h4>
                <p class="text-muted mb-3">
                    <span class="badge bg-secondary"><?= e($role['name']) ?></span>
                </p>
                <?php
                $statusClass = match($user['status']) {
                    'active' => 'bg-success',
                    'inactive' => 'bg-warning',
                    'blocked' => 'bg-danger',
                    default => 'bg-secondary'
                };
                ?>
                <span class="badge <?= $statusClass ?> mb-3"><?= ucfirst($user['status']) ?></span>
                
                <div class="d-grid gap-2 mt-4">
                    <?php if (can('users.edit')): ?>
                    <a href="<?= url('users/edit/' . $user['id']) ?>" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit User
                    </a>
                    <?php endif; ?>
                    <a href="<?= url('users') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Basic Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th width="30%">User ID:</th>
                        <td><code><?= e($user['user_id']) ?></code></td>
                    </tr>
                    <tr>
                        <th>Username:</th>
                        <td><?= e($user['username'] ?? 'Not set') ?></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><?= e($user['email'] ?? 'Not set') ?></td>
                    </tr>
                    <tr>
                        <th>Phone:</th>
                        <td><?= e($user['phone'] ?? 'Not set') ?></td>
                    </tr>
                    <tr>
                        <th>Gender:</th>
                        <td><?= e(ucfirst($user['gender'] ?? 'Not set')) ?></td>
                    </tr>
                    <tr>
                        <th>Address:</th>
                        <td><?= e($user['address'] ?? 'Not set') ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Role & Permissions</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th width="30%">Role:</th>
                        <td><?= e($role['name']) ?></td>
                    </tr>
                    <tr>
                        <th>Role Slug:</th>
                        <td><code><?= e($role['slug']) ?></code></td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            <span class="badge <?= $statusClass ?>"><?= ucfirst($user['status']) ?></span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Account Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th width="30%">Created By:</th>
                        <td>
                            <?php if ($created_by): ?>
                            <?= e($created_by['full_name']) ?> (<?= e($created_by['user_id']) ?>)
                            <?php else: ?>
                            System
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Created At:</th>
                        <td><?= date('F d, Y g:i A', strtotime($user['created_at'])) ?></td>
                    </tr>
                    <tr>
                        <th>Last Updated:</th>
                        <td><?= date('F d, Y g:i A', strtotime($user['updated_at'])) ?></td>
                    </tr>
                    <tr>
                        <th>Last Login:</th>
                        <td>
                            <?php if ($user['last_login_at']): ?>
                            <?= date('F d, Y g:i A', strtotime($user['last_login_at'])) ?>
                            <?php else: ?>
                            Never
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Last Login IP:</th>
                        <td><?= e($user['last_login_ip'] ?? 'Not recorded') ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <?php if (can('users.delete') || can('users.status')): ?>
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-flex gap-2 flex-wrap">
                    <?php if (can('users.status')): ?>
                    <button type="button" class="btn btn-outline-info dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-gear me-2"></i>Change Status
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
                    <button type="button" class="btn btn-outline-danger" onclick="deleteUser(<?= $user['id'] ?>)">
                        <i class="bi bi-trash me-2"></i>Delete User
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.profile-avatar img {
    object-fit: cover;
    border: 3px solid #dee2e6;
}
.avatar-placeholder {
    border: 3px solid #dee2e6;
}
</style>

<script>
function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        window.location.href = '<?= url('users/delete') ?>/' + userId;
    }
}
</script>
