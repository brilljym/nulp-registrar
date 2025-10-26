<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteConfirmModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirm Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="warning-icon mb-3">
                    <i class="fas fa-user-times text-danger" style="font-size: 3rem;"></i>
                </div>
                <h4 class="text-danger mb-3">Delete User Account</h4>
                <p class="mb-3">
                    Are you sure you want to delete the user: 
                    <strong id="deleteUserName" class="text-dark"></strong>?
                </p>
                <div class="alert alert-warning text-start">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone. All user data and associated records will be permanently removed from the system.
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary px-4 me-3" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>
                    Cancel
                </button>
                <button type="button" class="btn btn-danger px-4" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-2"></i>
                    Yes, Delete User
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Delete Confirmation Modal Styling */
#deleteConfirmModal .modal-content {
    border: none;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    overflow: hidden;
}

#deleteConfirmModal .modal-header {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    border: none;
    padding: 1.5rem;
}

#deleteConfirmModal .modal-title {
    font-weight: 600;
    font-size: 1.25rem;
}

#deleteConfirmModal .modal-body {
    padding: 2rem;
}

#deleteConfirmModal .warning-icon {
    animation: warningPulse 1s ease-in-out infinite alternate;
}

#deleteConfirmModal .modal-footer {
    border: none;
    padding: 1rem 2rem 2rem;
}

#deleteConfirmModal .btn {
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
    min-width: 130px;
}

#deleteConfirmModal .btn-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    border: none;
}

#deleteConfirmModal .btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
    background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
}

#deleteConfirmModal .btn-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
    border: none;
}

#deleteConfirmModal .btn-secondary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
}

#deleteConfirmModal .alert-warning {
    background-color: #fff3cd;
    border: 1px solid #ffeaa7;
    color: #856404;
    border-radius: 10px;
    font-size: 0.875rem;
}

/* Animation for warning icon */
@keyframes warningPulse {
    0% {
        transform: scale(1);
        opacity: 0.8;
    }
    100% {
        transform: scale(1.05);
        opacity: 1;
    }
}

/* Modal entrance animation */
#deleteConfirmModal.fade .modal-dialog {
    transform: scale(0.8) translateY(-20px);
    transition: all 0.3s ease;
}

#deleteConfirmModal.show .modal-dialog {
    transform: scale(1) translateY(0);
}

/* Loading state for delete button */
#confirmDeleteBtn.loading {
    pointer-events: none;
    opacity: 0.7;
}

#confirmDeleteBtn.loading .fas {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style><?php /**PATH D:\Nu-Regisv2\resources\views/admin/users/modals/delete.blade.php ENDPATH**/ ?>