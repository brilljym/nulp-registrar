<div class="modal fade" id="editUserModal<?php echo e($user->id); ?>" tabindex="-1" aria-labelledby="editUserModalLabel<?php echo e($user->id); ?>" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="<?php echo e(route('admin.users.update', $user->id)); ?>" method="POST" id="editUserForm<?php echo e($user->id); ?>">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <div class="modal-header">
          <h5 class="modal-title" id="editUserModalLabel<?php echo e($user->id); ?>">
            <span id="editStep1Title<?php echo e($user->id); ?>">Edit User</span>
            <span id="editStep2Title<?php echo e($user->id); ?>" style="display: none;">Step 2: Student Details</span>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <!-- Step Indicator -->
        <div class="px-4 pt-3" id="editStepIndicatorContainer<?php echo e($user->id); ?>" style="display: <?php echo e($user->role_id == 3 ? 'block' : 'none'); ?>;">
          <div class="d-flex justify-content-between mb-3 single-step" id="editStepIndicatorWrapper<?php echo e($user->id); ?>">
            <div class="step-indicator active" id="editIndicator1<?php echo e($user->id); ?>">
              <div class="step-circle">1</div>
              <div class="step-label">Basic Info</div>
            </div>
            <div class="step-line" style="display: none;"></div>
            <div class="step-indicator" id="editIndicator2<?php echo e($user->id); ?>" style="display: none;">
              <div class="step-circle">2</div>
              <div class="step-label">Student Details</div>
            </div>
          </div>
        </div>

        <div class="modal-body">
          <!-- Step 1: Basic User Information -->
          <div id="editStep1Content<?php echo e($user->id); ?>">
            <!-- Name Fields Row -->
            <div class="row">
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="first_name<?php echo e($user->id); ?>" class="form-label">First Name</label>
                  <input
                    type="text"
                    class="form-control"
                    id="first_name<?php echo e($user->id); ?>"
                    name="first_name"
                    value="<?php echo e(old('first_name', $user->first_name)); ?>"
                    required
                  >
                </div>
              </div>
              
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="middle_name<?php echo e($user->id); ?>" class="form-label">Middle Name <span class="text-muted">(optional)</span></label>
                  <input
                    type="text"
                    class="form-control"
                    id="middle_name<?php echo e($user->id); ?>"
                    name="middle_name"
                    value="<?php echo e(old('middle_name', $user->middle_name)); ?>"
                  >
                </div>
              </div>
              
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="last_name<?php echo e($user->id); ?>" class="form-label">Last Name</label>
                  <input
                    type="text"
                    class="form-control"
                    id="last_name<?php echo e($user->id); ?>"
                    name="last_name"
                    value="<?php echo e(old('last_name', $user->last_name)); ?>"
                    required
                  >
                </div>
              </div>
            </div>

            <!-- Email Fields Row -->
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="personal_email<?php echo e($user->id); ?>" class="form-label">Personal Email</label>
                  <input
                    type="email"
                    class="form-control"
                    id="personal_email<?php echo e($user->id); ?>"
                    name="personal_email"
                    value="<?php echo e(old('personal_email', $user->personal_email)); ?>"
                    required
                  >
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="school_email<?php echo e($user->id); ?>" class="form-label">School Email</label>
                  <input
                    type="email"
                    class="form-control"
                    id="school_email<?php echo e($user->id); ?>"
                    name="school_email"
                    value="<?php echo e(old('school_email', $user->school_email)); ?>"
                    readonly
                  >
                  <div class="form-text text-muted">Auto-generated from name fields</div>
                </div>
              </div>
            </div>

            <!-- Role Row -->
            <div class="mb-3">
              <label for="role_id<?php echo e($user->id); ?>" class="form-label">Role</label>
              <select name="role_id" id="role_id<?php echo e($user->id); ?>" class="form-select" required onchange="handleEditRoleChange<?php echo e($user->id); ?>(this.value)">
                <?php $__currentLoopData = \App\Models\Role::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($role->id); ?>" <?php if($user->role_id == $role->id): echo 'selected'; endif; ?>>
                    <?php echo e(strtoupper($role->name)); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
          </div>

          <!-- Step 2: Student Information (only visible for student role) -->
          <?php if($user->role_id == 3): ?>
          <div id="editStep2Content<?php echo e($user->id); ?>" style="display: none;">
            <div class="alert alert-info">
              <strong>Student Information Required</strong><br>
              Please provide the following details for the student profile.
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="course<?php echo e($user->id); ?>" class="form-label">Course</label>
                  <select name="course" id="course<?php echo e($user->id); ?>" class="form-select course-select" onchange="handleEditCourseChange<?php echo e($user->id); ?>(this)">
                    <option value="" disabled selected>-- Select Course --</option>
                    <option value="SACE" <?php if(old('course', $user->student->course ?? '') == 'SACE'): echo 'selected'; endif; ?>>School of Architecture and Civil Engineering (SACE)</option>
                    <option value="SAHS" <?php if(old('course', $user->student->course ?? '') == 'SAHS'): echo 'selected'; endif; ?>>School of Allied Health Sciences (SAHS)</option>
                    <option value="SABM" <?php if(old('course', $user->student->course ?? '') == 'SABM'): echo 'selected'; endif; ?>>School of Accountancy and Business Management (SABM)</option>
                    <option value="SHS" <?php if(old('course', $user->student->course ?? '') == 'SHS'): echo 'selected'; endif; ?>>Senior High School (SHS)</option>
                  </select>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="year_level<?php echo e($user->id); ?>" class="form-label">Year Level</label>
                  <select name="year_level" id="year_level<?php echo e($user->id); ?>" class="form-select year-level-select" disabled>
                    <option value="" disabled selected>-- Select Year Level --</option>
                  </select>
                </div>
              </div>
            </div>
            
            <div class="mb-3">
              <label for="department<?php echo e($user->id); ?>" class="form-label">Department</label>
              <select name="department" id="department<?php echo e($user->id); ?>" class="form-select department-select" disabled>
                <option value="" disabled selected>-- Select Department --</option>
              </select>
            </div>
          </div>
          <?php endif; ?>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-outline-secondary" id="editPrevBtn<?php echo e($user->id); ?>" style="display: none;" onclick="editPreviousStep<?php echo e($user->id); ?>()">Previous</button>
          <button type="button" class="btn btn-primary" id="editNextBtn<?php echo e($user->id); ?>" style="display: <?php echo e($user->role_id == 3 ? 'inline-block' : 'none'); ?>;" onclick="editNextStep<?php echo e($user->id); ?>()">Next</button>
          <button type="submit" class="btn btn-warning" id="editSubmitBtn<?php echo e($user->id); ?>" style="display: <?php echo e($user->role_id == 3 ? 'none' : 'inline-block'); ?>;">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
/* Step Indicator Styles */
.step-indicator {
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
}

.step-circle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background-color: #e9ecef;
  border: 2px solid #e9ecef;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  color: #6c757d;
  transition: all 0.3s ease;
}

.step-indicator.active .step-circle {
  background-color: #2c3192;
  border-color: #2c3192;
  color: white;
}

.step-indicator.completed .step-circle {
  background-color: #28a745;
  border-color: #28a745;
  color: white;
}

.step-label {
  font-size: 0.875rem;
  margin-top: 0.5rem;
  color: #6c757d;
  text-align: center;
}

.step-indicator.active .step-label {
  color: #2c3192;
  font-weight: 600;
}

.step-line {
  flex: 1;
  height: 2px;
  background-color: #e9ecef;
  margin: 20px 10px 0;
  transition: all 0.3s ease;
}

.step-line.completed {
  background-color: #28a745;
}

/* Single step layout when step 2 is hidden */
#editStepIndicatorWrapper<?php echo e($user->id); ?>.single-step {
  justify-content: center !important;
}

#editStepIndicatorWrapper<?php echo e($user->id); ?>.single-step .step-line {
  display: none !important;
}
</style>

<?php if($user->role_id == 3): ?>
<script>
let editCurrentStep<?php echo e($user->id); ?> = 1;
let editIsStudentRole<?php echo e($user->id); ?> = true;

function handleEditRoleChange<?php echo e($user->id); ?>(roleId) {
  editIsStudentRole<?php echo e($user->id); ?> = (roleId == '3'); // Student role ID is 3
  
  // Update next button text and behavior based on role
  const nextBtn = document.getElementById('editNextBtn<?php echo e($user->id); ?>');
  const submitBtn = document.getElementById('editSubmitBtn<?php echo e($user->id); ?>');
  const stepIndicator = document.getElementById('editIndicator2<?php echo e($user->id); ?>');
  const stepLine = document.querySelector('#editStepIndicatorWrapper<?php echo e($user->id); ?> .step-line');
  const stepWrapper = document.getElementById('editStepIndicatorWrapper<?php echo e($user->id); ?>');
  const step1Title = document.getElementById('editStep1Title<?php echo e($user->id); ?>');
  
  if (editIsStudentRole<?php echo e($user->id); ?>) {
    // Show step 2 for students - enable multi-step workflow
    step1Title.textContent = 'Step 1: Basic Information';
    nextBtn.textContent = 'Next';
    nextBtn.style.display = 'inline-block';
    submitBtn.style.display = 'none';
    stepIndicator.style.display = 'flex';
    stepLine.style.display = 'block';
    stepWrapper.classList.remove('single-step');
    
    // Initialize student fields as disabled
    document.getElementById('year_level<?php echo e($user->id); ?>').disabled = true;
    document.getElementById('department<?php echo e($user->id); ?>').disabled = true;
  } else {
    // Hide step 2 for all other roles - single step workflow
    step1Title.textContent = 'Edit User';
    nextBtn.style.display = 'none';
    submitBtn.style.display = 'inline-block';
    stepIndicator.style.display = 'none';
    stepLine.style.display = 'none';
    stepWrapper.classList.add('single-step');
  }
  
  // Update school email preview
  updateEditSchoolEmail<?php echo e($user->id); ?>();
}

function editNextStep<?php echo e($user->id); ?>() {
  if (editCurrentStep<?php echo e($user->id); ?> === 1 && validateEditStep1<?php echo e($user->id); ?>()) {
    if (editIsStudentRole<?php echo e($user->id); ?>) {
      editShowStep2<?php echo e($user->id); ?>();
    } else {
      // If not student role, submit directly
      document.getElementById('editUserForm<?php echo e($user->id); ?>').submit();
    }
  }
}

function editPreviousStep<?php echo e($user->id); ?>() {
  if (editCurrentStep<?php echo e($user->id); ?> === 2) {
    editShowStep1<?php echo e($user->id); ?>();
  }
}

function editShowStep1<?php echo e($user->id); ?>() {
  editCurrentStep<?php echo e($user->id); ?> = 1;
  document.getElementById('editStep1Content<?php echo e($user->id); ?>').style.display = 'block';
  document.getElementById('editStep2Content<?php echo e($user->id); ?>').style.display = 'none';
  
  document.getElementById('editStep1Title<?php echo e($user->id); ?>').style.display = 'inline';
  document.getElementById('editStep2Title<?php echo e($user->id); ?>').style.display = 'none';
  
  document.getElementById('editIndicator1<?php echo e($user->id); ?>').classList.add('active');
  document.getElementById('editIndicator2<?php echo e($user->id); ?>').classList.remove('active');
  document.getElementById('editIndicator1<?php echo e($user->id); ?>').classList.remove('completed');
  
  document.getElementById('editPrevBtn<?php echo e($user->id); ?>').style.display = 'none';
  document.getElementById('editNextBtn<?php echo e($user->id); ?>').style.display = 'inline-block';
  document.getElementById('editNextBtn<?php echo e($user->id); ?>').textContent = 'Next';
  document.getElementById('editSubmitBtn<?php echo e($user->id); ?>').style.display = 'none';
  
  document.querySelector('#editStepIndicatorWrapper<?php echo e($user->id); ?> .step-line').classList.remove('completed');
}

function editShowStep2<?php echo e($user->id); ?>() {
  editCurrentStep<?php echo e($user->id); ?> = 2;
  document.getElementById('editStep1Content<?php echo e($user->id); ?>').style.display = 'none';
  document.getElementById('editStep2Content<?php echo e($user->id); ?>').style.display = 'block';
  
  document.getElementById('editStep1Title<?php echo e($user->id); ?>').style.display = 'none';
  document.getElementById('editStep2Title<?php echo e($user->id); ?>').style.display = 'inline';
  
  document.getElementById('editIndicator1<?php echo e($user->id); ?>').classList.remove('active');
  document.getElementById('editIndicator1<?php echo e($user->id); ?>').classList.add('completed');
  document.getElementById('editIndicator2<?php echo e($user->id); ?>').classList.add('active');
  
  document.getElementById('editPrevBtn<?php echo e($user->id); ?>').style.display = 'inline-block';
  document.getElementById('editNextBtn<?php echo e($user->id); ?>').style.display = 'none';
  document.getElementById('editSubmitBtn<?php echo e($user->id); ?>').style.display = 'inline-block';
  
  document.querySelector('#editStepIndicatorWrapper<?php echo e($user->id); ?> .step-line').classList.add('completed');
  
  // Make student fields required
  document.getElementById('course<?php echo e($user->id); ?>').required = true;
  document.getElementById('year_level<?php echo e($user->id); ?>').required = true;
  document.getElementById('department<?php echo e($user->id); ?>').required = true;
}

function validateEditStep1<?php echo e($user->id); ?>() {
  const requiredFields = ['first_name<?php echo e($user->id); ?>', 'last_name<?php echo e($user->id); ?>', 'personal_email<?php echo e($user->id); ?>', 'role_id<?php echo e($user->id); ?>'];
  let isValid = true;
  
  requiredFields.forEach(fieldId => {
    const field = document.getElementById(fieldId);
    if (!field.value.trim()) {
      field.classList.add('is-invalid');
      isValid = false;
    } else {
      field.classList.remove('is-invalid');
    }
  });
  
  // Validate email format
  const emailField = document.getElementById('personal_email<?php echo e($user->id); ?>');
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (emailField.value && !emailPattern.test(emailField.value)) {
    emailField.classList.add('is-invalid');
    isValid = false;
  }
  
  if (!isValid) {
    // Show validation message
    let errorMessage = document.getElementById('edit-step1-error<?php echo e($user->id); ?>');
    if (!errorMessage) {
      errorMessage = document.createElement('div');
      errorMessage.id = 'edit-step1-error<?php echo e($user->id); ?>';
      errorMessage.className = 'alert alert-danger mt-2';
      errorMessage.textContent = 'Please fill in all required fields correctly.';
      document.getElementById('editStep1Content<?php echo e($user->id); ?>').appendChild(errorMessage);
    }
  } else {
    // Remove error message if exists
    const errorMessage = document.getElementById('edit-step1-error<?php echo e($user->id); ?>');
    if (errorMessage) {
      errorMessage.remove();
    }
  }
  
  return isValid;
}

function updateEditSchoolEmail<?php echo e($user->id); ?>() {
  const firstName = document.getElementById('first_name<?php echo e($user->id); ?>').value;
  const middleName = document.getElementById('middle_name<?php echo e($user->id); ?>').value;
  const lastName = document.getElementById('last_name<?php echo e($user->id); ?>').value;
  
  if (firstName && lastName) {
    const fi = firstName.charAt(0).toLowerCase();
    const mi = middleName ? middleName.charAt(0).toLowerCase() : '';
    const ln = lastName.toLowerCase();
    
    const schoolEmail = mi 
      ? `${ln}${fi}${mi}@student.nu-lipa.edu.ph`
      : `${ln}${fi}@student.nu-lipa.edu.ph`;
    
    document.getElementById('school_email<?php echo e($user->id); ?>').value = schoolEmail;
  }
}

// Department options by course - matching onsite form exactly
if (!window.departmentsByCourse) {
  window.departmentsByCourse = {
    'SACE': [
        { value: 'Information Technology', text: 'Information Technology' },
        { value: 'Computer Science', text: 'Computer Science' },
        { value: 'Architecture', text: 'Architecture' },
        { value: 'Civil Engineering', text: 'Civil Engineering' }
    ],
    'SAHS': [
        { value: 'Medical Technology', text: 'Medical Technology' },
        { value: 'Nursing', text: 'Nursing' },
        { value: 'Psychology', text: 'Psychology' }
    ],
    'SABM': [
        { value: 'Accountancy', text: 'Accountancy' },
        { value: 'Business Administration', text: 'Business Administration' },
        { value: 'Marketing', text: 'Marketing' }
    ],
    'SHS': [
        { value: 'Senior High School', text: 'Senior High School' }
    ]
  };
}

function handleEditCourseChange<?php echo e($user->id); ?>(courseSelect) {
    const course = courseSelect.value;
    const yearLevelSelect = document.getElementById('year_level<?php echo e($user->id); ?>');
    const deptSelect = document.getElementById('department<?php echo e($user->id); ?>');
    
    // Reset department dropdown
    deptSelect.innerHTML = '<option value="" disabled selected>-- Select Department --</option>';
    
    // Reset and populate year level based on course
    yearLevelSelect.innerHTML = '<option value="" disabled selected>-- Select Year Level --</option>';
    
    const currentYearValue = '<?php echo e(old('year_level', $user->student->year_level ?? '')); ?>';
    const currentDeptValue = '<?php echo e(old('department', $user->student->department ?? '')); ?>';
    
    if (course === 'SHS') {
        // Senior High School - Grade 11 and 12
        const shsYears = [
            { value: 'Grade 11', text: 'Grade 11' },
            { value: 'Grade 12', text: 'Grade 12' }
        ];
        shsYears.forEach(function(year) {
            const option = document.createElement('option');
            option.value = year.value;
            option.textContent = year.text;
            if (year.value === currentYearValue) {
                option.selected = true;
            }
            yearLevelSelect.appendChild(option);
        });
        yearLevelSelect.disabled = false;
    } else if (course) {
        // College courses - 1st to 4th Year
        const collegeYears = [
            { value: '1st Year', text: '1st Year' },
            { value: '2nd Year', text: '2nd Year' },
            { value: '3rd Year', text: '3rd Year' },
            { value: '4th Year', text: '4th Year' }
        ];
        collegeYears.forEach(function(year) {
            const option = document.createElement('option');
            option.value = year.value;
            option.textContent = year.text;
            if (year.value === currentYearValue) {
                option.selected = true;
            }
            yearLevelSelect.appendChild(option);
        });
        yearLevelSelect.disabled = false;
    }
    
    // Populate department dropdown
    if (window.departmentsByCourse[course]) {
        window.departmentsByCourse[course].forEach(function(dept) {
            const option = document.createElement('option');
            option.value = dept.value;
            option.textContent = dept.text;
            if (dept.value === currentDeptValue) {
                option.selected = true;
            }
            deptSelect.appendChild(option);
        });
        deptSelect.disabled = false;
    } else {
        deptSelect.disabled = true;
    }
}

// Initialize on modal show
document.getElementById('editUserModal<?php echo e($user->id); ?>').addEventListener('shown.bs.modal', function() {
  // Initialize based on current role
  handleEditRoleChange<?php echo e($user->id); ?>(document.getElementById('role_id<?php echo e($user->id); ?>').value);
  
  // Add event listeners for real-time email generation
  const nameFields = ['first_name<?php echo e($user->id); ?>', 'middle_name<?php echo e($user->id); ?>', 'last_name<?php echo e($user->id); ?>'];
  nameFields.forEach(fieldId => {
    const field = document.getElementById(fieldId);
    if (field) {
      field.addEventListener('input', () => updateEditSchoolEmail<?php echo e($user->id); ?>());
    }
  });
  
  // Initialize student fields if student
  if (editIsStudentRole<?php echo e($user->id); ?>) {
    const courseSelect = document.getElementById('course<?php echo e($user->id); ?>');
    if (courseSelect && courseSelect.value) {
      handleEditCourseChange<?php echo e($user->id); ?>(courseSelect);
    }
  }
});

// Reset form when modal is closed
document.getElementById('editUserModal<?php echo e($user->id); ?>').addEventListener('hidden.bs.modal', function() {
  editShowStep1<?php echo e($user->id); ?>();
  editIsStudentRole<?php echo e($user->id); ?> = true;
  
  // Reset to initial state
  const stepIndicator = document.getElementById('editIndicator2<?php echo e($user->id); ?>');
  const stepLine = document.querySelector('#editStepIndicatorWrapper<?php echo e($user->id); ?> .step-line');
  const stepWrapper = document.getElementById('editStepIndicatorWrapper<?php echo e($user->id); ?>');
  const step1Title = document.getElementById('editStep1Title<?php echo e($user->id); ?>');
  const step2Title = document.getElementById('editStep2Title<?php echo e($user->id); ?>');
  
  // Hide step 2 elements
  stepIndicator.style.display = 'none';
  stepLine.style.display = 'none';
  stepWrapper.classList.add('single-step');
  
  // Reset titles
  step1Title.textContent = 'Step 1: Basic Information';
  step1Title.style.display = 'inline';
  step2Title.style.display = 'none';
  
  // Reset button display to single-step mode
  document.getElementById('editNextBtn<?php echo e($user->id); ?>').style.display = 'none';
  document.getElementById('editPrevBtn<?php echo e($user->id); ?>').style.display = 'none';
  document.getElementById('editSubmitBtn<?php echo e($user->id); ?>').style.display = 'inline-block';
  
  // Remove validation classes
  document.querySelectorAll('#editUserForm<?php echo e($user->id); ?> .is-invalid').forEach(el => el.classList.remove('is-invalid'));
  // Remove error messages
  const errorMessage = document.getElementById('edit-step1-error<?php echo e($user->id); ?>');
  if (errorMessage) {
    errorMessage.remove();
  }
});
</script>
<?php endif; ?>
<?php /**PATH D:\Nu-Regisv2\resources\views\admin\users\modals\edit.blade.php ENDPATH**/ ?>