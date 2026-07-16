@extends('superadmin.layouts.app')
@section('title','Create User')
@section('page-title','Create User & Assign Role')
@section('page-sub','Only Super Admin can create accounts and assign roles')
@section('content')
<div style="max-width:760px;">
<div class="alert al-y an"><i class="fas fa-info-circle"></i><span><strong>Role Assignment Policy:</strong> Users cannot self-register. Only the Super Admin creates accounts and assigns roles (Student, Officer, Admin, Super Admin).</span></div>
<form method="POST" action="{{ route('superadmin.users.store') }}">@csrf
<div class="card an mb3"><div class="ch"><i class="fas fa-user-shield" style="color:var(--y);"></i><h2>Account Information</h2></div><div class="cb">
<div class="g2">
<div class="fg"><label class="fl">Full Name <span style="color:var(--danger)">*</span></label><input type="text" name="name" class="fc" value="{{ old('name') }}" placeholder="Juan Dela Cruz" required>@error('name')<div style="color:var(--danger);font-size:12px;margin-top:3px;">{{ $message }}</div>@enderror</div>
<div class="fg"><label class="fl">Email Address <span style="color:var(--danger)">*</span></label><input type="email" name="email" class="fc" value="{{ old('email') }}" placeholder="user@isams.edu.ph" required>@error('email')<div style="color:var(--danger);font-size:12px;margin-top:3px;">{{ $message }}</div>@enderror</div>
</div>
<div class="g2">
<div class="fg"><label class="fl">Password <span style="color:var(--danger)">*</span></label><input type="password" name="password" class="fc" placeholder="Min 8 characters" required>@error('password')<div style="color:var(--danger);font-size:12px;margin-top:3px;">{{ $message }}</div>@enderror</div>
<div class="fg"><label class="fl">Confirm Password <span style="color:var(--danger)">*</span></label><input type="password" name="password_confirmation" class="fc" placeholder="Repeat password" required></div>
</div>
<div class="fg"><label class="fl">Department / Office</label><input type="text" name="department" class="fc" value="{{ old('department') }}" placeholder="e.g. Scholarship Office, IT Department"></div>
</div></div>

<div class="card an mb3"><div class="ch"><i class="fas fa-shield-alt" style="color:var(--y);"></i><h2>Assign Role</h2></div><div class="cb">
<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:14px;">
@foreach([['student','Student','fas fa-user-graduate','Can browse scholarships, apply, check AI eligibility, request counseling.'],['officer','Officer','fas fa-user-tie','Can manage applications, run AI filter, schedule counseling sessions.'],['admin','Admin','fas fa-user-shield','Full access to scholarship management, reports, announcements.'],['superadmin','Super Admin','fas fa-crown','Full system access including user management and monitoring.']] as $role)
<label style="display:flex;align-items:flex-start;gap:12px;padding:13px;border:2px solid {{ old('role','student')===$role[0]?'var(--y)':'var(--bd)' }};border-radius:var(--rs);cursor:pointer;transition:all .2s;background:var(--card2);"
onmouseover="this.style.borderColor='var(--y)'" onmouseout="if(!this.querySelector('input').checked)this.style.borderColor='var(--bd)'">
<input type="radio" name="role" value="{{ $role[0] }}" {{ old('role','student')===$role[0]?'checked':'' }} style="margin-top:3px;accent-color:var(--y);"
onchange="document.querySelectorAll('.rl-l').forEach(l=>l.closest('label').style.borderColor='var(--bd)');this.closest('label').style.borderColor='var(--y)';toggleStudent('{{ $role[0] }}')">
<div><div style="display:flex;align-items:center;gap:8px;margin-bottom:5px;"><i class="{{ $role[2] }}" style="color:var(--y);font-size:16px;"></i><span class="fws rl-l" style="color:#fff;font-size:14px;">{{ $role[1] }}</span></div>
<div style="font-size:12px;color:var(--tm);line-height:1.4;">{{ $role[3] }}</div></div>
</label>
@endforeach
</div>
@error('role')<div style="color:var(--danger);font-size:12px;margin-bottom:10px;">{{ $message }}</div>@enderror
</div></div>

<div class="card an mb3" id="studentFields" style="{{ old('role','student')==='student'?'':'display:none;' }}">
<div class="ch"><i class="fas fa-graduation-cap" style="color:var(--y);"></i><h2>Student Information</h2><span style="font-size:12px;color:var(--tm);margin-left:8px;">(For Student role)</span></div>
<div class="cb">
<div class="g2">
<div class="fg"><label class="fl">Student ID</label><input type="text" name="student_id" class="fc mono" value="{{ old('student_id') }}" placeholder="2024-0001"></div>
<div class="fg"><label class="fl">Course</label><select name="course" class="fc"><option value="">Select Course</option>@foreach(['BS Computer Science','BS Information Technology','BS Engineering','BS Education','BS Nursing','BS Business Administration','AB Communication','BS Psychology','BS Accountancy','BS Tourism'] as $c)<option value="{{ $c }}">{{ $c }}</option>@endforeach</select></div>
</div>
<div class="g2">
<div class="fg"><label class="fl">Year Level</label><select name="year_level" class="fc"><option>1st Year</option><option>2nd Year</option><option>3rd Year</option><option>4th Year</option></select></div>
<div class="fg"><label class="fl">GWA</label><input type="number" name="gwa" class="fc mono" step="0.01" min="1.00" max="5.00" placeholder="1.75" value="{{ old('gwa') }}"></div>
</div>
</div></div>

<div style="display:flex;gap:10px;">
<button type="submit" class="btn btn-ac"><i class="fas fa-user-plus"></i> Create User & Assign Role</button>
<a href="{{ route('superadmin.users.index') }}" class="btn btn-o">Cancel</a>
</div>
</form>
</div>
@push('scripts')
<script>
function toggleStudent(role){document.getElementById('studentFields').style.display=role==='student'?'':'none';}
document.addEventListener('DOMContentLoaded',()=>{const c=document.querySelector('[name=role]:checked');if(c)toggleStudent(c.value);});
</script>
@endpush
@endsection
