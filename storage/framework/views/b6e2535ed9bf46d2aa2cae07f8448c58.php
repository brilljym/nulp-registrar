

<?php $__env->startSection('content'); ?>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">User Details</h4>
                    <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Basic Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Full Name:</td>
                                    <td><?php echo e($user->last_name); ?>, <?php echo e($user->first_name); ?> <?php echo e($user->middle_name); ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">School Email:</td>
                                    <td><?php echo e($user->school_email); ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Personal Email:</td>
                                    <td><?php echo e($user->personal_email); ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Role:</td>
                                    <td>
                                        <span class="badge bg-primary"><?php echo e(strtoupper($user->role->name ?? 'N/A')); ?></span>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <?php if($user->role_id == 3 && $user->student): ?>
                        <div class="col-md-6">
                            <h5>Student Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Student ID:</td>
                                    <td><?php echo e($user->student->student_id); ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Course:</td>
                                    <td><?php echo e($user->student->course); ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Department:</td>
                                    <td><?php echo e($user->student->department); ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Year Level:</td>
                                    <td><?php echo e($user->student->year_level); ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Mobile:</td>
                                    <td><?php echo e($user->student->mobile_number); ?></td>
                                </tr>
                            </table>
                        </div>
                        <?php endif; ?>

                        <?php if($user->role_id == 2 && $user->registrar): ?>
                        <div class="col-md-6">
                            <h5>Registrar Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Window Number:</td>
                                    <td><?php echo e($user->registrar->window_number); ?></td>
                                </tr>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-warning btn-sm edit-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editUserModal<?php echo e($user->id); ?>">
                                    <i class="fas fa-edit"></i> Edit User
                                </button>

                                <form method="POST" action="<?php echo e(route('admin.users.destroy', $user->id)); ?>" style="display:inline-block;">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this user?')">
                                        <i class="fas fa-trash"></i> Delete User
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <?php echo $__env->make('admin.users.modals.edit', ['user' => $user], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Nu-Regisv2\resources\views\admin\users\show.blade.php ENDPATH**/ ?>