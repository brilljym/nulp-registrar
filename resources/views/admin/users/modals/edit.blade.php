<div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="{{ route('admin.users.update', $user->id) }}" method="POST" id="editUserForm{{ $user->id }}">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title" id="editUserModalLabel{{ $user->id }}">
            <span id="editStep1Title{{ $user->id }}">Edit User</span>
            <span id="editStep2Title{{ $user->id }}" style="display: none;">Step 2: Student Details</span>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <!-- Step Indicator -->
        <div class="px-4 pt-3" id="editStepIndicatorContainer{{ $user->id }}" style="display: {{ $user->role_id == 3 ? 'block' : 'none' }};">
          <div class="d-flex justify-content-between mb-3 single-step" id="editStepIndicatorWrapper{{ $user->id }}">
            <div class="step-indicator active" id="editIndicator1{{ $user->id }}">
              <div class="step-circle">1</div>
              <div class="step-label">Basic Info</div>
            </div>
            <div class="step-line" style="display: none;"></div>
            <div class="step-indicator" id="editIndicator2{{ $user->id }}" style="display: none;">
              <div class="step-circle">2</div>
              <div class="step-label">Student Details</div>
            </div>
          </div>
        </div>

        <div class="modal-body">
          <!-- Step 1: Basic User Information -->
          <div id="editStep1Content{{ $user->id }}">
            <!-- Name Fields Row -->
            <div class="row">
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="first_name{{ $user->id }}" class="form-label">First Name</label>
                  <input
                    type="text"
                    class="form-control"
                    id="first_name{{ $user->id }}"
                    name="first_name"
                    value="{{ old('first_name', $user->first_name) }}"
                    required
                  >
                </div>
              </div>
              
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="middle_name{{ $user->id }}" class="form-label">Middle Name <span class="text-muted">(optional)</span></label>
                  <input
                    type="text"
                    class="form-control"
                    id="middle_name{{ $user->id }}"
                    name="middle_name"
                    value="{{ old('middle_name', $user->middle_name) }}"
                  >
                </div>
              </div>
              
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="last_name{{ $user->id }}" class="form-label">Last Name</label>
                  <input
                    type="text"
                    class="form-control"
                    id="last_name{{ $user->id }}"
                    name="last_name"
                    value="{{ old('last_name', $user->last_name) }}"
                    required
                  >
                </div>
              </div>
            </div>

            <!-- Email Fields Row -->
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="personal_email{{ $user->id }}" class="form-label">Personal Email</label>
                  <input
                    type="email"
                    class="form-control"
                    id="personal_email{{ $user->id }}"
                    name="personal_email"
                    value="{{ old('personal_email', $user->personal_email) }}"
                    required
                  >
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="school_email{{ $user->id }}" class="form-label">School Email</label>
                  <input
                    type="email"
                    class="form-control"
                    id="school_email{{ $user->id }}"
                    name="school_email"
                    value="{{ old('school_email', $user->school_email) }}"
                    readonly
                  >
                  <div class="form-text text-muted">Auto-generated from name fields</div>
                </div>
              </div>
            </div>

            <!-- Role Row -->
            <div class="mb-3">
              <label for="role_id{{ $user->id }}" class="form-label">Role</label>
              <select name="role_id" id="role_id{{ $user->id }}" class="form-select" required onchange="handleEditRoleChange{{ $user->id }}(this.value)">
                @foreach (\App\Models\Role::all() as $role)
                  <option value="{{ $role->id }}" @selected($user->role_id == $role->id)>
                    {{ strtoupper($role->name) }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>

          <!-- Step 2: Student Information (only visible for student role) -->
          @if($user->role_id == 3)
          <div id="editStep2Content{{ $user->id }}" style="display: none;">
            <div class="alert alert-info">
              <strong>Student Information Required</strong><br>
              Please provide the following details for the student profile.
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="course{{ $user->id }}" class="form-label">Course</label>
                  <select name="course" id="course{{ $user->id }}" class="form-select course-select" onchange="handleEditCourseChange{{ $user->id }}(this)">
                    <option value="" disabled selected>-- Select Course --</option>
                    <option value="SACE" @selected(old('course', $user->student->course ?? '') == 'SACE')>School of Architecture and Civil Engineering (SACE)</option>
                    <option value="SAHS" @selected(old('course', $user->student->course ?? '') == 'SAHS')>School of Allied Health Sciences (SAHS)</option>
                    <option value="SABM" @selected(old('course', $user->student->course ?? '') == 'SABM')>School of Accountancy and Business Management (SABM)</option>
                    <option value="SHS" @selected(old('course', $user->student->course ?? '') == 'SHS')>Senior High School (SHS)</option>
                  </select>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="year_level{{ $user->id }}" class="form-label">Year Level</label>
                  <select name="year_level" id="year_level{{ $user->id }}" class="form-select year-level-select" disabled>
                    <option value="" disabled selected>-- Select Year Level --</option>
                  </select>
                </div>
              </div>
            </div>
            
            <div class="mb-3">
              <label for="department{{ $user->id }}" class="form-label">Department</label>
              <select name="department" id="department{{ $user->id }}" class="form-select department-select" disabled>
                <option value="" disabled selected>-- Select Department --</option>
              </select>
            </div>
          </div>
          @endif
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-outline-secondary" id="editPrevBtn{{ $user->id }}" style="display: none;" onclick="editPreviousStep{{ $user->id }}()">Previous</button>
          <button type="button" class="btn btn-primary" id="editNextBtn{{ $user->id }}" style="display: {{ $user->role_id == 3 ? 'inline-block' : 'none' }};" onclick="editNextStep{{ $user->id }}()">Next</button>
          <button type="submit" class="btn btn-warning" id="editSubmitBtn{{ $user->id }}" style="display: {{ $user->role_id == 3 ? 'none' : 'inline-block' }};">Update</button>
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
#editStepIndicatorWrapper{{ $user->id }}.single-step {
  justify-content: center !important;
}

#editStepIndicatorWrapper{{ $user->id }}.single-step .step-line {
  display: none !important;
}
</style>

@if($user->role_id == 3)
<script>
let editCurrentStep{{ $user->id }} = 1;
let editIsStudentRole{{ $user->id }} = true;

function handleEditRoleChange{{ $user->id }}(roleId) {
  editIsStudentRole{{ $user->id }} = (roleId == '3'); // Student role ID is 3
  
  // Update next button text and behavior based on role
  const nextBtn = document.getElementById('editNextBtn{{ $user->id }}');
  const submitBtn = document.getElementById('editSubmitBtn{{ $user->id }}');
  const stepIndicator = document.getElementById('editIndicator2{{ $user->id }}');
  const stepLine = document.querySelector('#editStepIndicatorWrapper{{ $user->id }} .step-line');
  const stepWrapper = document.getElementById('editStepIndicatorWrapper{{ $user->id }}');
  const step1Title = document.getElementById('editStep1Title{{ $user->id }}');
  
  if (editIsStudentRole{{ $user->id }}) {
    // Show step 2 for students - enable multi-step workflow
    step1Title.textContent = 'Step 1: Basic Information';
    nextBtn.textContent = 'Next';
    nextBtn.style.display = 'inline-block';
    submitBtn.style.display = 'none';
    stepIndicator.style.display = 'flex';
    stepLine.style.display = 'block';
    stepWrapper.classList.remove('single-step');
    
    // Initialize student fields as disabled
    document.getElementById('year_level{{ $user->id }}').disabled = true;
    document.getElementById('department{{ $user->id }}').disabled = true;
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
  updateEditSchoolEmail{{ $user->id }}();
}

function editNextStep{{ $user->id }}() {
  if (editCurrentStep{{ $user->id }} === 1 && validateEditStep1{{ $user->id }}()) {
    if (editIsStudentRole{{ $user->id }}) {
      editShowStep2{{ $user->id }}();
    } else {
      // If not student role, submit directly
      document.getElementById('editUserForm{{ $user->id }}').submit();
    }
  }
}

function editPreviousStep{{ $user->id }}() {
  if (editCurrentStep{{ $user->id }} === 2) {
    editShowStep1{{ $user->id }}();
  }
}

function editShowStep1{{ $user->id }}() {
  editCurrentStep{{ $user->id }} = 1;
  document.getElementById('editStep1Content{{ $user->id }}').style.display = 'block';
  document.getElementById('editStep2Content{{ $user->id }}').style.display = 'none';
  
  document.getElementById('editStep1Title{{ $user->id }}').style.display = 'inline';
  document.getElementById('editStep2Title{{ $user->id }}').style.display = 'none';
  
  document.getElementById('editIndicator1{{ $user->id }}').classList.add('active');
  document.getElementById('editIndicator2{{ $user->id }}').classList.remove('active');
  document.getElementById('editIndicator1{{ $user->id }}').classList.remove('completed');
  
  document.getElementById('editPrevBtn{{ $user->id }}').style.display = 'none';
  document.getElementById('editNextBtn{{ $user->id }}').style.display = 'inline-block';
  document.getElementById('editNextBtn{{ $user->id }}').textContent = 'Next';
  document.getElementById('editSubmitBtn{{ $user->id }}').style.display = 'none';
  
  document.querySelector('#editStepIndicatorWrapper{{ $user->id }} .step-line').classList.remove('completed');
}

function editShowStep2{{ $user->id }}() {
  editCurrentStep{{ $user->id }} = 2;
  document.getElementById('editStep1Content{{ $user->id }}').style.display = 'none';
  document.getElementById('editStep2Content{{ $user->id }}').style.display = 'block';
  
  document.getElementById('editStep1Title{{ $user->id }}').style.display = 'none';
  document.getElementById('editStep2Title{{ $user->id }}').style.display = 'inline';
  
  document.getElementById('editIndicator1{{ $user->id }}').classList.remove('active');
  document.getElementById('editIndicator1{{ $user->id }}').classList.add('completed');
  document.getElementById('editIndicator2{{ $user->id }}').classList.add('active');
  
  document.getElementById('editPrevBtn{{ $user->id }}').style.display = 'inline-block';
  document.getElementById('editNextBtn{{ $user->id }}').style.display = 'none';
  document.getElementById('editSubmitBtn{{ $user->id }}').style.display = 'inline-block';
  
  document.querySelector('#editStepIndicatorWrapper{{ $user->id }} .step-line').classList.add('completed');
  
  // Make student fields required
  document.getElementById('course{{ $user->id }}').required = true;
  document.getElementById('year_level{{ $user->id }}').required = true;
  document.getElementById('department{{ $user->id }}').required = true;
}

function validateEditStep1{{ $user->id }}() {
  const requiredFields = ['first_name{{ $user->id }}', 'last_name{{ $user->id }}', 'personal_email{{ $user->id }}', 'role_id{{ $user->id }}'];
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
  const emailField = document.getElementById('personal_email{{ $user->id }}');
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (emailField.value && !emailPattern.test(emailField.value)) {
    emailField.classList.add('is-invalid');
    isValid = false;
  }
  
  if (!isValid) {
    // Show validation message
    let errorMessage = document.getElementById('edit-step1-error{{ $user->id }}');
    if (!errorMessage) {
      errorMessage = document.createElement('div');
      errorMessage.id = 'edit-step1-error{{ $user->id }}';
      errorMessage.className = 'alert alert-danger mt-2';
      errorMessage.textContent = 'Please fill in all required fields correctly.';
      document.getElementById('editStep1Content{{ $user->id }}').appendChild(errorMessage);
    }
  } else {
    // Remove error message if exists
    const errorMessage = document.getElementById('edit-step1-error{{ $user->id }}');
    if (errorMessage) {
      errorMessage.remove();
    }
  }
  
  return isValid;
}

function updateEditSchoolEmail{{ $user->id }}() {
  const firstName = document.getElementById('first_name{{ $user->id }}').value;
  const middleName = document.getElementById('middle_name{{ $user->id }}').value;
  const lastName = document.getElementById('last_name{{ $user->id }}').value;
  
  if (firstName && lastName) {
    const fi = firstName.charAt(0).toLowerCase();
    const mi = middleName ? middleName.charAt(0).toLowerCase() : '';
    const ln = lastName.toLowerCase();
    
    const schoolEmail = mi 
      ? `${ln}${fi}${mi}@student.nu-lipa.edu.ph`
      : `${ln}${fi}@student.nu-lipa.edu.ph`;
    
    document.getElementById('school_email{{ $user->id }}').value = schoolEmail;
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

function handleEditCourseChange{{ $user->id }}(courseSelect) {
    const course = courseSelect.value;
    const yearLevelSelect = document.getElementById('year_level{{ $user->id }}');
    const deptSelect = document.getElementById('department{{ $user->id }}');
    
    // Reset department dropdown
    deptSelect.innerHTML = '<option value="" disabled selected>-- Select Department --</option>';
    
    // Reset and populate year level based on course
    yearLevelSelect.innerHTML = '<option value="" disabled selected>-- Select Year Level --</option>';
    
    const currentYearValue = '{{ old('year_level', $user->student->year_level ?? '') }}';
    const currentDeptValue = '{{ old('department', $user->student->department ?? '') }}';
    
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
document.getElementById('editUserModal{{ $user->id }}').addEventListener('shown.bs.modal', function() {
  // Initialize based on current role
  handleEditRoleChange{{ $user->id }}(document.getElementById('role_id{{ $user->id }}').value);
  
  // Add event listeners for real-time email generation
  const nameFields = ['first_name{{ $user->id }}', 'middle_name{{ $user->id }}', 'last_name{{ $user->id }}'];
  nameFields.forEach(fieldId => {
    const field = document.getElementById(fieldId);
    if (field) {
      field.addEventListener('input', () => updateEditSchoolEmail{{ $user->id }}());
    }
  });
  
  // Initialize student fields if student
  if (editIsStudentRole{{ $user->id }}) {
    const courseSelect = document.getElementById('course{{ $user->id }}');
    if (courseSelect && courseSelect.value) {
      handleEditCourseChange{{ $user->id }}(courseSelect);
    }
  }
});

// Reset form when modal is closed
document.getElementById('editUserModal{{ $user->id }}').addEventListener('hidden.bs.modal', function() {
  editShowStep1{{ $user->id }}();
  editIsStudentRole{{ $user->id }} = true;
  
  // Reset to initial state
  const stepIndicator = document.getElementById('editIndicator2{{ $user->id }}');
  const stepLine = document.querySelector('#editStepIndicatorWrapper{{ $user->id }} .step-line');
  const stepWrapper = document.getElementById('editStepIndicatorWrapper{{ $user->id }}');
  const step1Title = document.getElementById('editStep1Title{{ $user->id }}');
  const step2Title = document.getElementById('editStep2Title{{ $user->id }}');
  
  // Hide step 2 elements
  stepIndicator.style.display = 'none';
  stepLine.style.display = 'none';
  stepWrapper.classList.add('single-step');
  
  // Reset titles
  step1Title.textContent = 'Step 1: Basic Information';
  step1Title.style.display = 'inline';
  step2Title.style.display = 'none';
  
  // Reset button display to single-step mode
  document.getElementById('editNextBtn{{ $user->id }}').style.display = 'none';
  document.getElementById('editPrevBtn{{ $user->id }}').style.display = 'none';
  document.getElementById('editSubmitBtn{{ $user->id }}').style.display = 'inline-block';
  
  // Remove validation classes
  document.querySelectorAll('#editUserForm{{ $user->id }} .is-invalid').forEach(el => el.classList.remove('is-invalid'));
  // Remove error messages
  const errorMessage = document.getElementById('edit-step1-error{{ $user->id }}');
  if (errorMessage) {
    errorMessage.remove();
  }
});
</script>
@endif
