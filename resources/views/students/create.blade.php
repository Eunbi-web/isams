@extends('layouts.app')
@section('title', 'Enroll Student')
@section('page-title', 'Enroll New Student')
@section('page-subtitle', 'Add a new student record to the system')

@section('content')
<div style="max-width:860px;">
<form method="POST" action="{{ route('students.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="card animate mb-3">
        <div class="card-header">
            <i class="fas fa-user-circle" style="color:var(--primary-light);"></i>
            <h2>Personal Information</h2>
        </div>
        <div class="card-body">
            <div class="grid-3">
                <div class="form-group">
                    <label class="form-label">First Name <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                           value="{{ old('first_name') }}" placeholder="Maria" required>
                    @error('first_name')<div style="color:var(--danger);font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Middle Name</label>
                    <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name') }}" placeholder="Santos">
                </div>
                <div class="form-group">
                    <label class="form-label">Last Name <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" placeholder="Dela Cruz" required>
                </div>
            </div>
            <div class="grid-3">
                <div class="form-group">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="birthdate" class="form-control" value="{{ old('birthdate') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Sex</label>
                    <select name="sex" class="form-control">
                        <option value="">Select</option>
                        <option value="Male" {{ old('sex')=='Male'?'selected':'' }}>Male</option>
                        <option value="Female" {{ old('sex')=='Female'?'selected':'' }}>Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Civil Status</label>
                    <select name="civil_status" class="form-control">
                        <option value="Single">Single</option>
                        <option value="Married">Married</option>
                    </select>
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Contact Number</label>
                    <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number') }}" placeholder="09XX-XXX-XXXX">
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="student@email.com">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="2" placeholder="Complete home address">{{ old('address') }}</textarea>
            </div>
        </div>
    </div>

    <div class="card animate mb-3">
        <div class="card-header">
            <i class="fas fa-graduation-cap" style="color:var(--accent);"></i>
            <h2>Academic Information</h2>
        </div>
        <div class="card-body">
            <div class="grid-3">
                <div class="form-group">
                    <label class="form-label">Student ID <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="student_id" class="form-control font-mono" value="{{ old('student_id') }}" placeholder="2024-0001" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Course / Program <span style="color:var(--danger)">*</span></label>
                    <select name="course" class="form-control" required>
                        <option value="">Select Course</option>
                        @foreach([
                            'BS Computer Science','BS Information Technology','BS Engineering',
                            'BS Education','BS Nursing','BS Business Administration',
                            'AB Communication','AB Political Science','BS Psychology',
                            'BS Social Work','BS Accountancy','BS Tourism'
                        ] as $c)
                        <option value="{{ $c }}" {{ old('course')==$c?'selected':'' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Year Level <span style="color:var(--danger)">*</span></label>
                    <select name="year_level" class="form-control" required>
                        <option value="">Select Year</option>
                        <option value="1st Year">1st Year</option>
                        <option value="2nd Year">2nd Year</option>
                        <option value="3rd Year">3rd Year</option>
                        <option value="4th Year">4th Year</option>
                        <option value="5th Year">5th Year</option>
                    </select>
                </div>
            </div>
            <div class="grid-3">
                <div class="form-group">
                    <label class="form-label">Section</label>
                    <input type="text" name="section" class="form-control" value="{{ old('section') }}" placeholder="A">
                </div>
                <div class="form-group">
                    <label class="form-label">Academic Year</label>
                    <input type="text" name="academic_year" class="form-control" value="{{ old('academic_year', '2023-2024') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Semester</label>
                    <select name="semester" class="form-control">
                        <option value="1st">1st Semester</option>
                        <option value="2nd">2nd Semester</option>
                        <option value="Summer">Summer</option>
                    </select>
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Enrollment Type</label>
                    <select name="enrollment_type" class="form-control">
                        <option value="Regular">Regular</option>
                        <option value="Irregular">Irregular</option>
                        <option value="Transferee">Transferee</option>
                        <option value="Returnee">Returnee</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">General Weighted Average (GWA)</label>
                    <input type="number" name="gwa" class="form-control" value="{{ old('gwa') }}" step="0.01" min="1.00" max="5.00" placeholder="1.75">
                </div>
            </div>
        </div>
    </div>

    <div class="card animate mb-3">
        <div class="card-header">
            <i class="fas fa-users" style="color:var(--success);"></i>
            <h2>Guardian / Parent Information</h2>
        </div>
        <div class="card-body">
            <div class="grid-3">
                <div class="form-group">
                    <label class="form-label">Guardian Name</label>
                    <input type="text" name="guardian_name" class="form-control" value="{{ old('guardian_name') }}" placeholder="Full name">
                </div>
                <div class="form-group">
                    <label class="form-label">Relationship</label>
                    <select name="guardian_relationship" class="form-control">
                        <option value="">Select</option>
                        <option>Father</option>
                        <option>Mother</option>
                        <option>Spouse</option>
                        <option>Sibling</option>
                        <option>Relative</option>
                        <option>Guardian</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Guardian Contact</label>
                    <input type="text" name="guardian_contact" class="form-control" value="{{ old('guardian_contact') }}" placeholder="09XX-XXX-XXXX">
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enroll Student</button>
        <a href="{{ route('students.index') }}" class="btn btn-outline"><i class="fas fa-times"></i> Cancel</a>
    </div>
</form>
</div>
@endsection
