<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="successModalLabel">
                    <i class="fas fa-check-circle me-2"></i>
                    <span id="successTitle">Success!</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="success-icon mb-3">
                    <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                </div>
                <h4 class="text-success mb-3" id="successMessage">Operation completed successfully!</h4>
                <p class="text-muted mb-0" id="successDescription">The requested action has been performed successfully.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-success px-4" data-bs-dismiss="modal">
                    <i class="fas fa-thumbs-up me-2"></i>
                    Great!
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Success Modal Styling */
#successModal .modal-content {
    border: none;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    overflow: hidden;
}

#successModal .modal-header {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
    padding: 1.5rem;
}

#successModal .modal-title {
    font-weight: 600;
    font-size: 1.25rem;
}

#successModal .modal-body {
    padding: 2rem;
}

#successModal .success-icon {
    animation: successPulse 0.6s ease-in-out;
}

#successModal .modal-footer {
    border: none;
    padding: 1rem 2rem 2rem;
}

#successModal .btn-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
}

#successModal .btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
}

/* Animation for success icon */
@keyframes successPulse {
    0% {
        transform: scale(0.8);
        opacity: 0;
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

/* Modal entrance animation */
#successModal.fade .modal-dialog {
    transform: scale(0.8) translateY(-20px);
    transition: all 0.3s ease;
}

#successModal.show .modal-dialog {
    transform: scale(1) translateY(0);
}
</style>

<script>
// Fallback showSuccessModal function in case external JS doesn't load
if (typeof showSuccessModal === 'undefined') {
    function showSuccessModal(type, message = null, description = null) {
        const modal = new bootstrap.Modal(document.getElementById('successModal'));
        const titleElement = document.getElementById('successTitle');
        const messageElement = document.getElementById('successMessage');
        const descriptionElement = document.getElementById('successDescription');
        
        const messages = {
            create: {
                title: 'User Created Successfully!',
                message: message || 'New user has been created successfully!',
                description: description || 'The user account has been set up and is ready to use.'
            },
            update: {
                title: 'User Updated Successfully!',
                message: message || 'User information has been updated successfully!',
                description: description || 'All changes have been saved and applied to the user account.'
            },
            delete: {
                title: 'User Deleted Successfully!',
                message: message || 'User has been deleted successfully!',
                description: description || 'The user account has been removed from the system.'
            }
        };
        
        const config = messages[type] || messages.create;
        
        titleElement.textContent = config.title;
        messageElement.textContent = config.message;
        descriptionElement.textContent = config.description;
        
        modal.show();
        
        setTimeout(() => {
            if (modal._isShown) {
                modal.hide();
            }
        }, 3000);
    }
}

// Show success modal based on session flash data (for regular form submissions)
document.addEventListener('DOMContentLoaded', function() {
    <?php if(session('success')): ?>
        <?php
            $successMessage = session('success');
            $operationType = 'create';
            
            if (str_contains($successMessage, 'updated')) {
                $operationType = 'update';
            } elseif (str_contains($successMessage, 'deleted')) {
                $operationType = 'delete';
            }
        ?>
        
        showSuccessModal('<?php echo e($operationType); ?>', '<?php echo e($successMessage); ?>');
    <?php endif; ?>
});
</script><?php /**PATH D:\Nu-Regisv2\resources\views\admin\users\modals\success.blade.php ENDPATH**/ ?>