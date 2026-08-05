<?php if (has_flash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= e(flash('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">My Profile</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= url('profile/edit') ?>" class="btn btn-primary">
            <i class="bi bi-pencil me-2"></i>Edit Profile
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card text-center">
            <div class="card-body">
                <?php if ($user['avatar']): ?>
                    <img src="<?= e($user['avatar']) ?>" alt="Avatar" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                <?php else: ?>
                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 150px; height: 150px;">
                        <i class="bi bi-person fs-1 text-white"></i>
                    </div>
                <?php endif; ?>
                
                <h4><?= e($user['full_name']) ?></h4>
                <p class="text-muted"><?= e($user['user_id']) ?></p>
                <span class="badge bg-primary"><?= e($role['name']) ?></span>
            </div>
        </div>
    </div>
    
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Profile Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th width="30%">User ID:</th>
                        <td><?= e($user['user_id']) ?></td>
                    </tr>
                    <tr>
                        <th>Full Name:</th>
                        <td><?= e($user['full_name']) ?></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><?= e($user['email'] ?? 'Not provided') ?></td>
                    </tr>
                    <tr>
                        <th>Phone:</th>
                        <td><?= e($user['phone'] ?? 'Not provided') ?></td>
                    </tr>
                    <tr>
                        <th>Address:</th>
                        <td><?= e($user['address'] ?? 'Not provided') ?></td>
                    </tr>
                    <tr>
                        <th>Role:</th>
                        <td><?= e($role['name']) ?></td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            <?php if ($user['is_active']): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Last Login:</th>
                        <td><?= $user['last_login_at'] ? date('M d, Y H:i', strtotime($user['last_login_at'])) : 'Never' ?></td>
                    </tr>
                    <tr>
                        <th>Member Since:</th>
                        <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-flex gap-2">
                    <a href="<?= url('profile/edit') ?>" class="btn btn-outline-primary">
                        <i class="bi bi-pencil me-2"></i>Edit Profile
                    </a>
                    <a href="<?= url('profile/change-password') ?>" class="btn btn-outline-warning">
                        <i class="bi bi-key me-2"></i>Change Password
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
