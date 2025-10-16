@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="mb-4">
        <h3 class="mb-0 text-center">Student Profile</h3>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('student.profile.update') }}">
        @csrf

        <!-- Student Details Section -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="mb-3"><i class="bi bi-person-fill"></i> Student Details</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="student_id" class="form-label">Student ID</label>
                        <input type="text" class="form-control" id="student_id" value="{{ $student->student_id }}" disabled>
                    </div>

                    <div class="col-md-4">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="full_name" value="{{ $user->last_name }}, {{ $user->first_name }} {{ $user->middle_name }}" disabled>
                    </div>

                    <div class="col-md-4">
                        <label for="school_email" class="form-label">School Email</label>
                        <input type="text" class="form-control" id="school_email" value="{{ $user->school_email }}" disabled>
                    </div>

                    <div class="col-md-4">
                        <label for="course" class="form-label">Course</label>
                        <input type="text" class="form-control" name="course" value="{{ old('course', $student->course) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label for="year_level" class="form-label">Year Level</label>
                        <input type="text" class="form-control" name="year_level" value="{{ old('year_level', $student->year_level) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label for="department" class="form-label">Department</label>
                        <input type="text" class="form-control" name="department" value="{{ old('department', $student->department) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label for="mobile_number" class="form-label">Mobile Number</label>
                        <input type="text" class="form-control" name="mobile_number" value="{{ old('mobile_number', $student->mobile_number) }}" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Address Section -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="mb-3"><i class="bi bi-geo-alt-fill"></i> Address Information</h5>
                <div class="row g-3">
                    <div class="col-md-2">
                        <label for="house_number" class="form-label">House No. (optional)</label>
                        <input type="text" class="form-control" name="house_number" value="{{ old('house_number', $student->house_number) }}">
                    </div>

                    <div class="col-md-2">
                        <label for="block_number" class="form-label">Block No. (optional)</label>
                        <input type="text" class="form-control" name="block_number" value="{{ old('block_number', $student->block_number) }}">
                    </div>

                    <div class="col-md-4">
                        <label for="street" class="form-label">Street</label>
                        <input type="text" class="form-control" name="street" value="{{ old('street', $student->street) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label for="region" class="form-label">Region</label>
                        <select class="form-control" name="region" id="region" required>
                            <option value="">Select Region</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="province" class="form-label">Province</label>
                        <select class="form-control" name="province" id="province" required>
                            <option value="">Select Province</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="city" class="form-label">City/Municipality</label>
                        <select class="form-control" name="city" id="city" required>
                            <option value="">Select City/Municipality</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="barangay" class="form-label">Barangay</label>
                        <select class="form-control" name="barangay" id="barangay" required>
                            <option value="">Select Barangay</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 text-end">
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </div>
    </form>
</div>

<script>
const PSGC_BASE_URL = 'https://psgc.gitlab.io/api';

document.addEventListener('DOMContentLoaded', function() {
    loadRegions();
    
    // Event listeners
    document.getElementById('region').addEventListener('change', function() {
        const regionCode = this.selectedOptions[0]?.getAttribute('data-code');
        loadProvinces(regionCode);
        loadCities(regionCode, null);
    });
    
    document.getElementById('province').addEventListener('change', function() {
        const provinceCode = this.selectedOptions[0]?.getAttribute('data-code');
        if (provinceCode) {
            loadCities(null, provinceCode);
        } else {
            // If no province selected, load cities under region
            const regionCode = document.getElementById('region').selectedOptions[0]?.getAttribute('data-code');
            loadCities(regionCode, null);
        }
    });
    
    document.getElementById('city').addEventListener('change', function() {
        loadBarangays(this.selectedOptions[0]?.getAttribute('data-code'));
    });
});

function loadRegions() {
    fetch(`${PSGC_BASE_URL}/regions.json`)
        .then(response => response.json())
        .then(data => {
            const regionSelect = document.getElementById('region');
            data.forEach(region => {
                const option = document.createElement('option');
                option.value = region.name;
                option.setAttribute('data-code', region.code);
                option.textContent = region.name;
                regionSelect.appendChild(option);
            });
            // Pre-select if value exists
            const currentRegion = "{{ old('region', $student->region ?? '') }}";
            if (currentRegion) {
                regionSelect.value = currentRegion;
                regionSelect.dispatchEvent(new Event('change'));
            }
        })
        .catch(error => console.error('Error loading regions:', error));
}

function loadProvinces(regionCode) {
    if (!regionCode) return;
    fetch(`${PSGC_BASE_URL}/regions/${regionCode}/provinces.json`)
        .then(response => response.json())
        .then(data => {
            const provinceSelect = document.getElementById('province');
            provinceSelect.innerHTML = '<option value="">Select Province</option>';
            data.forEach(province => {
                const option = document.createElement('option');
                option.value = province.name;
                option.setAttribute('data-code', province.code);
                option.textContent = province.name;
                provinceSelect.appendChild(option);
            });
            // Pre-select if value exists
            const currentProvince = "{{ old('province', $student->province ?? '') }}";
            if (currentProvince) {
                provinceSelect.value = currentProvince;
                provinceSelect.dispatchEvent(new Event('change'));
            }
        })
        .catch(error => console.error('Error loading provinces:', error));
}

function loadCities(regionCode, provinceCode) {
    let url;
    if (provinceCode) {
        url = `${PSGC_BASE_URL}/provinces/${provinceCode}/cities-municipalities.json`;
    } else if (regionCode) {
        url = `${PSGC_BASE_URL}/regions/${regionCode}/cities-municipalities.json`;
    } else {
        return;
    }
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            const citySelect = document.getElementById('city');
            citySelect.innerHTML = '<option value="">Select City/Municipality</option>';
            data.forEach(city => {
                const option = document.createElement('option');
                option.value = city.name;
                option.setAttribute('data-code', city.code);
                option.textContent = city.name;
                citySelect.appendChild(option);
            });
            // Pre-select if value exists
            const currentCity = "{{ old('city', $student->city ?? '') }}";
            if (currentCity) {
                citySelect.value = currentCity;
                citySelect.dispatchEvent(new Event('change'));
            }
        })
        .catch(error => console.error('Error loading cities:', error));
}

function loadBarangays(cityCode) {
    if (!cityCode) return;
    fetch(`${PSGC_BASE_URL}/cities-municipalities/${cityCode}/barangays.json`)
        .then(response => response.json())
        .then(data => {
            const barangaySelect = document.getElementById('barangay');
            barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
            data.forEach(barangay => {
                const option = document.createElement('option');
                option.value = barangay.name;
                option.setAttribute('data-code', barangay.code);
                option.textContent = barangay.name;
                barangaySelect.appendChild(option);
            });
            // Pre-select if value exists
            const currentBarangay = "{{ old('barangay', $student->barangay ?? '') }}";
            if (currentBarangay) {
                barangaySelect.value = currentBarangay;
            }
        })
        .catch(error => console.error('Error loading barangays:', error));
}

function clearSelect(selectId) {
    const select = document.getElementById(selectId);
    select.innerHTML = `<option value="">Select ${selectId.charAt(0).toUpperCase() + selectId.slice(1)}</option>`;
}
</script>
@endsection
