// User Management JavaScript with Success Modals
document.addEventListener('DOMContentLoaded', function() {
    
    // Handle Create User Form Submission
    const createUserForm = document.getElementById('createUserForm');
    if (createUserForm) {
        createUserForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Ensure student fields are enabled before submission
            const yearLevelField = document.getElementById('year_level');
            const departmentField = document.getElementById('department');
            if (yearLevelField) yearLevelField.disabled = false;
            if (departmentField) departmentField.disabled = false;
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            
            // Show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating...';
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close the create modal
                    const createModal = bootstrap.Modal.getInstance(document.getElementById('createUserModal'));
                    createModal.hide();
                    
                    // Reset form
                    createUserForm.reset();
                    
                    // Show success modal
                    showSuccessModal(data.type, data.message);
                    
                    // Refresh the page after modal is hidden to show new user
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    // Handle validation errors
                    if (data.errors) {
                        displayFormErrors(data.errors, createUserForm);
                    } else {
                        alert('An error occurred while creating the user.');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while creating the user.');
            })
            .finally(() => {
                // Restore button state
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            });
        });
    }
    
    // Handle Edit User Form Submissions
    const editUserForms = document.querySelectorAll('form[action*="users"][method="POST"]:has(input[name="_method"][value="PUT"])');
    editUserForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            
            // Show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close the edit modal
                    const modalElement = this.closest('.modal');
                    const editModal = bootstrap.Modal.getInstance(modalElement);
                    editModal.hide();
                    
                    // Show success modal
                    showSuccessModal(data.type, data.message);
                    
                    // Refresh the page after modal is hidden to show updated data
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    // Handle validation errors
                    if (data.errors) {
                        displayFormErrors(data.errors, form);
                    } else {
                        alert('An error occurred while updating the user.');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the user.');
            })
            .finally(() => {
                // Restore button state
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            });
        });
    });
    
    // Handle Delete User Actions
    const deleteButtons = document.querySelectorAll('.delete-btn');
    let currentDeleteForm = null;
    let currentDeleteButton = null;
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const form = this.closest('form');
            const userName = this.closest('tr').querySelector('.fw-semibold').textContent.trim();
            
            // Store references for later use
            currentDeleteForm = form;
            currentDeleteButton = this;
            
            // Show custom confirmation modal
            showDeleteConfirmModal(userName);
        });
    });
    
    // Handle delete confirmation
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            if (!currentDeleteForm || !currentDeleteButton) return;
            
            const formData = new FormData(currentDeleteForm);
            const originalText = currentDeleteButton.innerHTML;
            
            // Show loading state on the confirm button
            this.classList.add('loading');
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Deleting...';
            this.disabled = true;
            
            // Also show loading state on the original delete button
            currentDeleteButton.disabled = true;
            currentDeleteButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            fetch(currentDeleteForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hide the delete confirmation modal
                    const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteConfirmModal'));
                    deleteModal.hide();
                    
                    // Show success modal
                    showSuccessModal(data.type, data.message);
                    
                    // Remove the row from table with animation
                    const row = currentDeleteButton.closest('tr');
                    row.style.transition = 'opacity 0.3s ease';
                    row.style.opacity = '0';
                    
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    alert('An error occurred while deleting the user.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the user.');
            })
            .finally(() => {
                // Restore button states
                this.classList.remove('loading');
                this.innerHTML = '<i class="fas fa-trash me-2"></i>Yes, Delete User';
                this.disabled = false;
                
                currentDeleteButton.disabled = false;
                currentDeleteButton.innerHTML = originalText;
                
                // Clear references
                currentDeleteForm = null;
                currentDeleteButton = null;
            });
        });
    }
});

// Function to display form validation errors
function displayFormErrors(errors, form) {
    // Clear previous errors
    form.querySelectorAll('.is-invalid').forEach(input => {
        input.classList.remove('is-invalid');
    });
    form.querySelectorAll('.invalid-feedback').forEach(feedback => {
        feedback.remove();
    });
    
    // Display new errors
    Object.keys(errors).forEach(fieldName => {
        const field = form.querySelector(`[name="${fieldName}"]`);
        if (field) {
            field.classList.add('is-invalid');
            
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = errors[fieldName][0];
            
            field.parentNode.appendChild(errorDiv);
        }
    });
}

// Function to clear form errors
function clearFormErrors(form) {
    form.querySelectorAll('.is-invalid').forEach(input => {
        input.classList.remove('is-invalid');
    });
    form.querySelectorAll('.invalid-feedback').forEach(feedback => {
        feedback.remove();
    });
}

// Enhanced success modal function with more customization
function showSuccessModal(type, message = null, description = null, autoHide = true) {
    const modal = new bootstrap.Modal(document.getElementById('successModal'));
    const titleElement = document.getElementById('successTitle');
    const messageElement = document.getElementById('successMessage');
    const descriptionElement = document.getElementById('successDescription');
    
    // Set default messages based on operation type
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
            description: description || 'The user account and all associated data have been removed from the system.'
        }
    };
    
    const config = messages[type] || messages.create;
    
    titleElement.textContent = config.title;
    messageElement.textContent = config.message;
    descriptionElement.textContent = config.description;
    
    modal.show();
    
    // Auto-hide after 3 seconds (optional)
    if (autoHide) {
        setTimeout(() => {
            if (modal._isShown) {
                modal.hide();
            }
        }, 3000);
    }
}

// Function to show delete confirmation modal
function showDeleteConfirmModal(userName) {
    const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    const userNameElement = document.getElementById('deleteUserName');
    
    if (userNameElement) {
        userNameElement.textContent = userName;
    }
    
    modal.show();
}