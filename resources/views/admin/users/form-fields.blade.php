<div class="mb-3">
  <label for="first_name" class="form-label">First Name</label>
  <input
    type="text"
    class="form-control"
    id="first_name"
    name="first_name"
    value="{{ old('first_name', $user->first_name ?? '') }}"
    required
  >
</div>

<div class="mb-3">
  <label for="middle_name" class="form-label">Middle Name <span class="text-muted">(optional)</span></label>
  <input
    type="text"
    class="form-control"
    id="middle_name"
    name="middle_name"
    value="{{ old('middle_name', $user->middle_name ?? '') }}"
  >
</div>

<div class="mb-3">
  <label for="last_name" class="form-label">Last Name</label>
  <input
    type="text"
    class="form-control"
    id="last_name"
    name="last_name"
    value="{{ old('last_name', $user->last_name ?? '') }}"
    required
  >
</div>

<div class="mb-3">
  <label for="school_email" class="form-label">School Email</label>
  <input
    type="email"
    class="form-control"
    id="school_email"
    name="school_email"
    value="{{ old('school_email', $user->school_email ?? 'auto-generated') }}"
    readonly
  >
  <div class="form-text text-muted">This will be generated automatically from name fields.</div>
</div>

<div class="mb-3">
  <label for="personal_email" class="form-label">Personal Email</label>
  <input
    type="email"
    class="form-control"
    id="personal_email"
    name="personal_email"
    value="{{ old('personal_email', $user->personal_email ?? '') }}"
    required
  >
</div>

@empty($user)
<div class="mb-3">
  <label for="password" class="form-label">Password</label>
  <input
    type="password"
    class="form-control"
    id="password"
    name="password"
    required
  >
</div>
@endempty

<div class="mb-3">
  <label for="role_id" class="form-label">Role</label>
  <select name="role_id" id="role_id" class="form-select" required onchange="toggleStudentFields(this.value)">
    @foreach (\App\Models\Role::all() as $role)
      <option value="{{ $role->id }}" @selected(isset($user) && $user->role_id == $role->id)>
        {{ strtoupper($role->name) }}
      </option>
    @endforeach
  </select>
</div>

<!-- Student-specific fields (only show if role is Student) -->
<div id="student-fields" style="display: {{ (isset($user) && $user->role_id == 3) || (!isset($user) && old('role_id') == 3) ? 'block' : 'none' }};">
  <hr class="my-4">
  <h6 class="mb-3 text-primary">Student Information</h6>
  
  <div class="row">
    <div class="col-md-6">
      <div class="mb-3">
        <label for="course" class="form-label">Course</label>
        <select name="course" id="course_edit_{{ $user->id ?? 'create' }}" class="form-select course-select">
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
        <label for="year_level" class="form-label">Year Level</label>
        <select name="year_level" id="year_level_edit_{{ $user->id ?? 'create' }}" class="form-select year-level-select" disabled>
          <option value="" disabled selected>-- Select Year Level --</option>
        </select>
      </div>
    </div>
  </div>
  
  <div class="mb-3">
    <label for="department" class="form-label">Department</label>
    <select name="department" id="department_edit_{{ $user->id ?? 'create' }}" class="form-select department-select" disabled>
      <option value="" disabled selected>-- Select Department --</option>
    </select>
  </div>
</div>

<script>
// Department options by course - matching onsite form exactly
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
        { value: 'Academic Track - STEM', text: 'Academic Track - STEM' },
        { value: 'Academic Track - ABM', text: 'Academic Track - ABM' },
        { value: 'Academic Track - HUMSS', text: 'Academic Track - HUMSS' },
        { value: 'Academic Track - GAS', text: 'Academic Track - GAS' }
    ]
};

function toggleStudentFields(roleId) {
  const studentFields = document.getElementById('student-fields');
  const courseField = document.querySelector('.course-select');
  const yearLevelField = document.querySelector('.year-level-select');
  const departmentField = document.querySelector('.department-select');
  
  if (roleId == '3') { // Student role ID is 3
    studentFields.style.display = 'block';
    if (courseField) {
      courseField.required = true;
      // Initialize department and year level as disabled
      if (yearLevelField) yearLevelField.disabled = true;
      if (departmentField) departmentField.disabled = true;
      
      // If there's already a course selected, populate dependent fields
      if (courseField.value) {
        handleCourseChangeGeneric(courseField, courseField.value);
      }
    }
  } else {
    studentFields.style.display = 'none';
    if (courseField) {
      courseField.required = false;
      courseField.value = '';
    }
    if (yearLevelField) {
      yearLevelField.required = false;
      yearLevelField.value = '';
      yearLevelField.disabled = true;
    }
    if (departmentField) {
      departmentField.required = false;
      departmentField.value = '';
      departmentField.disabled = true;
    }
  }
}

function handleCourseChangeGeneric(courseSelect, course) {
    // Find the corresponding year level and department selects
    const modal = courseSelect.closest('.modal-content') || courseSelect.closest('#student-fields');
    const yearLevelSelect = modal.querySelector('.year-level-select');
    const deptSelect = modal.querySelector('.department-select');
    
    if (!yearLevelSelect || !deptSelect) return;
    
    // Reset department dropdown
    deptSelect.innerHTML = '<option value="" disabled selected>-- Select Department --</option>';
    
    // Reset and populate year level based on course
    yearLevelSelect.innerHTML = '<option value="" disabled selected>-- Select Year Level --</option>';
    
    const currentYearValue = '{{ old('year_level', $user->student->year_level ?? '') }}';
    const currentDeptValue = '{{ old('department', $user->student->department ?? '') }}';
    
    if (course === 'SHS') {
        // Senior High School - Grade 11 and 12
        var shsYears = [
            { value: 'Grade 11', text: 'Grade 11' },
            { value: 'Grade 12', text: 'Grade 12' }
        ];
        shsYears.forEach(function(year) {
            var option = document.createElement('option');
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
        var collegeYears = [
            { value: '1st Year', text: '1st Year' },
            { value: '2nd Year', text: '2nd Year' },
            { value: '3rd Year', text: '3rd Year' },
            { value: '4th Year', text: '4th Year' }
        ];
        collegeYears.forEach(function(year) {
            var option = document.createElement('option');
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
    if (window.departmentsByCourse && window.departmentsByCourse[course]) {
        window.departmentsByCourse[course].forEach(function(dept) {
            var option = document.createElement('option');
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

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
  const roleSelect = document.getElementById('role_id');
  if (roleSelect && roleSelect.value) {
    toggleStudentFields(roleSelect.value);
  }
  
  // Initialize department and year level as disabled on page load
  document.querySelectorAll('.department-select').forEach(select => {
    select.disabled = true;
  });
  
  document.querySelectorAll('.year-level-select').forEach(select => {
    select.disabled = true;
  });
  
  // Add event delegation for course changes
  document.addEventListener('change', function(e) {
    if (e.target.classList.contains('course-select')) {
      handleCourseChangeGeneric(e.target, e.target.value);
    }
  });
});
</script></div>
