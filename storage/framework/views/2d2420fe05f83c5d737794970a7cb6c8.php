<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="<?php echo e(route('admin.users.store')); ?>" method="POST" id="createUserForm">
        <?php echo csrf_field(); ?>
        <div class="modal-header">
          <h5 class="modal-title" id="createUserModalLabel">
            <span id="step1Title">Add New User</span>
            <span id="step2Title" style="display: none;">Step 2: Student Details</span>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <!-- Step Indicator -->
        <div class="px-4 pt-3" id="stepIndicatorContainer">
          <div class="d-flex justify-content-between mb-3 single-step" id="stepIndicatorWrapper">
            <div class="step-indicator active" id="indicator1">
              <div class="step-circle">1</div>
              <div class="step-label">Basic Info</div>
            </div>
            <div class="step-line" style="display: none;"></div>
            <div class="step-indicator" id="indicator2" style="display: none;">
              <div class="step-circle">2</div>
              <div class="step-label">Student Details</div>
            </div>
          </div>
        </div>

        <div class="modal-body">
          <!-- Step 1: Basic User Information -->
          <div id="step1Content">
            <!-- Name Fields Row -->
            <div class="row">
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="first_name" class="form-label">First Name</label>
                  <input
                    type="text"
                    class="form-control"
                    id="first_name"
                    name="first_name"
                    value="<?php echo e(old('first_name')); ?>"
                    required
                  >
                </div>
              </div>
              
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="middle_name" class="form-label">Middle Name <span class="text-muted">(optional)</span></label>
                  <input
                    type="text"
                    class="form-control"
                    id="middle_name"
                    name="middle_name"
                    value="<?php echo e(old('middle_name')); ?>"
                  >
                </div>
              </div>
              
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="last_name" class="form-label">Last Name</label>
                  <input
                    type="text"
                    class="form-control"
                    id="last_name"
                    name="last_name"
                    value="<?php echo e(old('last_name')); ?>"
                    required
                  >
                </div>
              </div>
            </div>

            <!-- Email Fields Row -->
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="personal_email" class="form-label">Personal Email</label>
                  <input
                    type="email"
                    class="form-control"
                    id="personal_email"
                    name="personal_email"
                    value="<?php echo e(old('personal_email')); ?>"
                    required
                  >
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="school_email" class="form-label">School Email</label>
                  <input
                    type="email"
                    class="form-control"
                    id="school_email"
                    name="school_email"
                    value="<?php echo e(old('school_email', 'auto-generated')); ?>"
                    readonly
                  >
                  <div class="form-text text-muted">Auto-generated from name fields</div>
                </div>
              </div>
            </div>

            <!-- Role Selection -->
            <div class="mb-3">
              <label for="role_id" class="form-label">Role</label>
              <select name="role_id" id="role_id" class="form-select" required onchange="handleRoleChange(this.value)">
                <option value="" disabled selected>-- Select Role --</option>
                <?php $__currentLoopData = \App\Models\Role::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($role->id); ?>" <?php if(old('role_id') == $role->id): echo 'selected'; endif; ?>>
                    <?php echo e(strtoupper($role->name)); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <div class="form-text text-muted">A random password will be generated and sent to the school email</div>
            </div>
          </div>

          <!-- Step 2: Student Information (only visible for student role) -->
          <div id="step2Content" style="display: none;">
            <div class="alert alert-info">
              <strong>Student Information Required</strong><br>
              Please provide the following details for the student profile.
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="course" class="form-label">Course</label>
                  <select name="course" id="course" class="form-select course-select" onchange="updateDepartmentOptions()">
                    <option value="" disabled selected>-- Select Course --</option>
                    <option value="SACE" <?php if(old('course') == 'SACE'): echo 'selected'; endif; ?>>School of Architecture and Civil Engineering (SACE)</option>
                    <option value="SAHS" <?php if(old('course') == 'SAHS'): echo 'selected'; endif; ?>>School of Allied Health Sciences (SAHS)</option>
                    <option value="SABM" <?php if(old('course') == 'SABM'): echo 'selected'; endif; ?>>School of Accountancy and Business Management (SABM)</option>
                    <option value="SHS" <?php if(old('course') == 'SHS'): echo 'selected'; endif; ?>>Senior High School (SHS)</option>
                  </select>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="year_level" class="form-label">Year Level</label>
                  <select name="year_level" id="year_level" class="form-select year-level-select" disabled>
                    <option value="" disabled selected>-- Select Year Level --</option>
                  </select>
                </div>
              </div>
            </div>
            
            <div class="mb-3">
              <label for="department" class="form-label">Department</label>
              <select name="department" id="department" class="form-select department-select" disabled>
                <option value="" disabled selected>-- Select Department --</option>
              </select>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-outline-secondary" id="prevBtn" style="display: none;" onclick="previousStep()">Previous</button>
          <button type="button" class="btn btn-primary" id="nextBtn" style="display: none;" onclick="nextStep()">Next</button>
          <button type="submit" class="btn btn-success" id="submitBtn">Create User</button>
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
#stepIndicatorWrapper.single-step {
  justify-content: center !important;
}

#stepIndicatorWrapper.single-step .step-line {
  display: none !important;
}
</style>

<script>
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

let currentStep = 1;
let isStudentRole = false;

function handleRoleChange(roleId) {
  isStudentRole = (roleId == '3'); // Student role ID is 3
  
  // Update next button text and behavior based on role
  const nextBtn = document.getElementById('nextBtn');
  const submitBtn = document.getElementById('submitBtn');
  const stepIndicator = document.getElementById('indicator2');
  const stepLine = document.querySelector('.step-line');
  const stepWrapper = document.getElementById('stepIndicatorWrapper');
  const step1Title = document.getElementById('step1Title');
  
  if (isStudentRole) {
    // Show step 2 for students - enable multi-step workflow
    step1Title.textContent = 'Step 1: Basic Information';
    nextBtn.textContent = 'Next';
    nextBtn.style.display = 'inline-block';
    submitBtn.style.display = 'none';
    stepIndicator.style.display = 'flex';
    stepLine.style.display = 'block';
    stepWrapper.classList.remove('single-step');
    
    // Initialize student fields as disabled
    document.getElementById('year_level').disabled = true;
    document.getElementById('department').disabled = true;
  } else {
    // Hide step 2 for all other roles - single step workflow
    step1Title.textContent = 'Add New User';
    nextBtn.style.display = 'none';
    submitBtn.style.display = 'inline-block';
    stepIndicator.style.display = 'none';
    stepLine.style.display = 'none';
    stepWrapper.classList.add('single-step');
  }
  
  // Update school email preview
  updateSchoolEmail();
}

function nextStep() {
  if (currentStep === 1 && validateStep1()) {
    if (isStudentRole) {
      showStep2();
    } else {
      // If not student role, submit directly
      document.getElementById('createUserForm').submit();
    }
  }
}

function previousStep() {
  if (currentStep === 2) {
    showStep1();
  }
}

function showStep1() {
  currentStep = 1;
  document.getElementById('step1Content').style.display = 'block';
  document.getElementById('step2Content').style.display = 'none';
  
  document.getElementById('step1Title').style.display = 'inline';
  document.getElementById('step2Title').style.display = 'none';
  
  document.getElementById('indicator1').classList.add('active');
  document.getElementById('indicator2').classList.remove('active');
  document.getElementById('indicator1').classList.remove('completed');
  
  document.getElementById('prevBtn').style.display = 'none';
  document.getElementById('nextBtn').style.display = 'inline-block';
  document.getElementById('nextBtn').textContent = 'Next';
  document.getElementById('submitBtn').style.display = 'none';
  
  document.querySelector('.step-line').classList.remove('completed');
}

function showStep2() {
  currentStep = 2;
  document.getElementById('step1Content').style.display = 'none';
  document.getElementById('step2Content').style.display = 'block';
  
  document.getElementById('step1Title').style.display = 'none';
  document.getElementById('step2Title').style.display = 'inline';
  
  document.getElementById('indicator1').classList.remove('active');
  document.getElementById('indicator1').classList.add('completed');
  document.getElementById('indicator2').classList.add('active');
  
  document.getElementById('prevBtn').style.display = 'inline-block';
  document.getElementById('nextBtn').style.display = 'none';
  document.getElementById('submitBtn').style.display = 'inline-block';
  
  document.querySelector('.step-line').classList.add('completed');
  
  // Make student fields required
  document.getElementById('course').required = true;
  document.getElementById('year_level').required = true;
  document.getElementById('department').required = true;
}

function validateStep1() {
  const requiredFields = ['first_name', 'last_name', 'personal_email', 'role_id'];
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
  const emailField = document.getElementById('personal_email');
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (emailField.value && !emailPattern.test(emailField.value)) {
    emailField.classList.add('is-invalid');
    isValid = false;
  }
  
  if (!isValid) {
    // Show validation message
    let errorMessage = document.getElementById('step1-error');
    if (!errorMessage) {
      errorMessage = document.createElement('div');
      errorMessage.id = 'step1-error';
      errorMessage.className = 'alert alert-danger mt-2';
      errorMessage.textContent = 'Please fill in all required fields correctly.';
      document.getElementById('step1Content').appendChild(errorMessage);
    }
  } else {
    // Remove error message if exists
    const errorMessage = document.getElementById('step1-error');
    if (errorMessage) {
      errorMessage.remove();
    }
  }
  
  return isValid;
}

function updateSchoolEmail() {
  const firstName = document.getElementById('first_name').value;
  const middleName = document.getElementById('middle_name').value;
  const lastName = document.getElementById('last_name').value;
  
  if (firstName && lastName) {
    const fi = firstName.charAt(0).toLowerCase();
    const mi = middleName ? middleName.charAt(0).toLowerCase() : '';
    const ln = lastName.toLowerCase();
    
    const schoolEmail = mi 
      ? `${ln}${fi}${mi}@student.nu-lipa.edu.ph`
      : `${ln}${fi}@student.nu-lipa.edu.ph`;
    
    document.getElementById('school_email').value = schoolEmail;
  }
}

function updateDepartmentOptions() {
  const course = document.getElementById('course').value;
  const yearLevelSelect = document.getElementById('year_level');
  const deptSelect = document.getElementById('department');
  
  // Clear existing options
  yearLevelSelect.innerHTML = '<option value="" disabled selected>-- Select Year Level --</option>';
  deptSelect.innerHTML = '<option value="" disabled selected>-- Select Department --</option>';
  
  if (course === 'SHS') {
    // Senior High School - Grades 11-12
    const shsYears = [
      { value: 'Grade 11', text: 'Grade 11' },
      { value: 'Grade 12', text: 'Grade 12' }
    ];
    shsYears.forEach(function(year) {
      const option = document.createElement('option');
      option.value = year.value;
      option.textContent = year.text;
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
      yearLevelSelect.appendChild(option);
    });
    yearLevelSelect.disabled = false;
  } else {
    yearLevelSelect.disabled = true;
  }
  
  // Populate department dropdown
  if (window.departmentsByCourse && window.departmentsByCourse[course]) {
    window.departmentsByCourse[course].forEach(function(dept) {
      const option = document.createElement('option');
      option.value = dept.value;
      option.textContent = dept.text;
      deptSelect.appendChild(option);
    });
    deptSelect.disabled = false;
  } else {
    deptSelect.disabled = true;
  }
}

// Add event listeners for real-time email generation
document.addEventListener('DOMContentLoaded', function() {
  const nameFields = ['first_name', 'middle_name', 'last_name'];
  nameFields.forEach(fieldId => {
    const field = document.getElementById(fieldId);
    if (field) {
      field.addEventListener('input', updateSchoolEmail);
    }
  });
  
  // Reset form when modal is closed
  document.getElementById('createUserModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('createUserForm').reset();
    showStep1();
    isStudentRole = false;
    
    // Reset to initial single-step state
    const stepIndicator = document.getElementById('indicator2');
    const stepLine = document.querySelector('.step-line');
    const stepWrapper = document.getElementById('stepIndicatorWrapper');
    const step1Title = document.getElementById('step1Title');
    const step2Title = document.getElementById('step2Title');
    
    // Hide step 2 elements
    stepIndicator.style.display = 'none';
    stepLine.style.display = 'none';
    stepWrapper.classList.add('single-step');
    
    // Reset titles
    step1Title.textContent = 'Add New User';
    step1Title.style.display = 'inline';
    step2Title.style.display = 'none';
    
    // Reset button display to single-step mode
    document.getElementById('nextBtn').style.display = 'none';
    document.getElementById('prevBtn').style.display = 'none';
    document.getElementById('submitBtn').style.display = 'inline-block';
    
    // Remove validation classes
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    // Remove error messages
    const errorMessage = document.getElementById('step1-error');
    if (errorMessage) {
      errorMessage.remove();
    }
  });
});
</script>
<?php /**PATH D:\Nu-Regisv2\resources\views/admin/users/modals/create.blade.php ENDPATH**/ ?>