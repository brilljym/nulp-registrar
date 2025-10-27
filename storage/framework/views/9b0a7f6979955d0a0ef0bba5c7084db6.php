

<?php $__env->startSection('content'); ?>
<style>
    .student-profile-card {
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        border-left: 5px solid #2c3192;
        margin-bottom: 2rem;
    }

    .profile-header {
        background: linear-gradient(135deg, #2c3192 0%, #1e2570 100%);
        color: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        text-align: center;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, #ffd600 0%, #e6b800 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 3rem;
        font-weight: bold;
        color: #2c3192;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .info-section {
        background: #fff;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border: 1px solid #e9ecef;
    }

    .info-label {
        font-weight: 600;
        color: #2c3192;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .info-value {
        font-size: 1.1rem;
        color: #495057;
        font-weight: 500;
    }

    .badge-custom {
        background: linear-gradient(135deg, #2c3192 0%, #1e2570 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .back-btn {
        background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
        border: none;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .back-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        color: white;
    }

    .stats-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 10px;
        padding: 1.5rem;
        text-align: center;
        margin-bottom: 1rem;
        border: 1px solid #dee2e6;
    }

    .stats-number {
        font-size: 2rem;
        font-weight: 700;
        color: #2c3192;
        margin-bottom: 0.5rem;
    }

    .stats-label {
        color: #6c757d;
        font-size: 0.9rem;
        font-weight: 500;
    }
</style>

<div class="container-fluid mt-4">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="<?php echo e(route('admin.students.index')); ?>" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            Back to Students
        </a>
    </div>

    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-avatar">
            <?php echo e(strtoupper(substr($student->user->first_name, 0, 1))); ?><?php echo e(strtoupper(substr($student->user->last_name, 0, 1))); ?>

        </div>
        <h2 class="mb-1"><?php echo e($student->user->first_name); ?> <?php echo e($student->user->middle_name); ?> <?php echo e($student->user->last_name); ?></h2>
        <p class="mb-0 opacity-75">Student ID: <?php echo e($student->student_id); ?></p>
    </div>

    <div class="row">
        <!-- Student Information -->
        <div class="col-lg-8">
            <div class="student-profile-card">
                <h4 class="mb-4" style="color: #2c3192; font-weight: 600;">
                    <i class="fas fa-user-graduate me-2"></i>Student Information
                </h4>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-section">
                            <div class="info-label">Student ID</div>
                            <div class="info-value"><?php echo e($student->student_id); ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-section">
                            <div class="info-label">Full Name</div>
                            <div class="info-value"><?php echo e($student->user->last_name); ?>, <?php echo e($student->user->first_name); ?> <?php echo e($student->user->middle_name); ?></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-section">
                            <div class="info-label">Department</div>
                            <div class="info-value">
                                <span class="badge-custom"><?php echo e(strtoupper($student->department ?? 'N/A')); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-section">
                            <div class="info-label">Course</div>
                            <div class="info-value"><?php echo e($student->course ?? 'N/A'); ?></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-section">
                            <div class="info-label">Year Level</div>
                            <div class="info-value">
                                <span class="badge badge-success"><?php echo e(strtoupper($student->year_level ?? 'N/A')); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-section">
                            <div class="info-label">Email</div>
                            <div class="info-value"><?php echo e($student->user->email ?? 'N/A'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Sidebar -->
        <div class="col-lg-4">
            <div class="stats-card">
                <div class="stats-number"><?php echo e($student->user->created_at->format('M Y')); ?></div>
                <div class="stats-label">Member Since</div>
            </div>

            <div class="stats-card">
                <div class="stats-number"><?php echo e($student->user->updated_at->diffForHumans()); ?></div>
                <div class="stats-label">Last Updated</div>
            </div>

            <div class="stats-card">
                <div class="stats-number"><?php echo e(\App\Models\OnsiteRequest::where('student_id', $student->id)->count()); ?></div>
                <div class="stats-label">Total Requests</div>
            </div>

            <div class="stats-card">
                <div class="stats-number"><?php echo e(\App\Models\OnsiteRequest::where('student_id', $student->id)->where('status', 'completed')->count()); ?></div>
                <div class="stats-label">Completed Requests</div>
            </div>
        </div>
    </div>
</div>

<!-- FontAwesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Nu-Regisv2\resources\views/admin/students/show.blade.php ENDPATH**/ ?>