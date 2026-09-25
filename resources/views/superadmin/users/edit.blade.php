@extends('superadmin.layouts.app')
@section('title','Edit User')
@section('page-title','Edit User & Role')
@section('page-sub','Update user information and role assignment')
@section('content')
<div style="max-width:720px;">
<div style="margin-bottom:14px;"><a href="{{ route('superadmin.users.index') }}" class="btn btn-o btn-sm"><i class="fas fa-arrow-left"></i> Back</a></div>
<form method="POST" action="{{ route('superadmin.users.update',$user->id) }}">@csrf @method('PUT')
<div class="card an mb3"><div class="ch">
<div class="av" style="width:40px;height:40px;font-size:15px;background:{{ $user->role==='superadmin'?'linear-gradient(135deg,var(--y),var(--yd))':'rgba(240,192,32,.15)' }};color:{{ $user->role==='superadmin'?'#0d3318':'var(--y)' }};">{{ strtoupper(substr($user->name,0,1)) }}</div>
<div><h2>{{ $user->name }}</h2><div style="font-size:12px;color:var(--tm);">{{ $user->email }}</div></div>
<div class="ch-acts"><span class="badge {{ $user->role==='superadmin'?'b-sa':($user->role==='student'?'b-g':($user->role==='admin'?'b-y':'b-i')) }}">{{ ucfirst($user->role) }}</span><span class="badge {{ $user->is_active?'b-s':'b-d' }}">{{ $user->is_active?'Active':'Inactive' }}</span></div>
</div><div class="cb">
<div class="g2">
<div class="fg"><label class="fl">Full Name</label><input type="text" name="name" class="fc" value="{{ old('name',$user->name) }}" required></div>
<div class="fg"><label class="fl">Email Address</label><input type="email" name="email" class="fc" value="{{ old('email',$user->email) }}" required></div>
</div>
<div class="g2">
<div class="fg"><label class="fl">New Password <span style="font-size:11px;color:var(--tm);font-weight:400;">(blank to keep)</span></label><input type="password" name="password" class="fc" placeholder="••••••••"></div>
<div class="fg"><label class="fl">Confirm Password</label><input type="password" name="password_confirmation" class="fc" placeholder="••••••••"></div>
</div>
<div class="g2">
<div class="fg"><label class="fl">Department</label><input type="text" name="department" class="fc" value="{{ old('department',$user->department) }}" placeholder="e.g. Scholarship Office"></div>
<div class="fg"><label class="fl">Account Status</label><select name="is_active" class="fc"><option value="1" {{ $user->is_active?'selected':'' }}>Active</option><option value="0" {{ !$user->is_active?'selected':'' }}>Inactive</option></select></div>
</div>
</div></div>

<div class="card an mb3"><div class="ch"><i class="fas fa-shield-alt" style="color:var(--y);"></i><h2>Role Assignment</h2></div><div class="cb">
<div class="fg">
<label class="fl">Role <span style="color:var(--danger)">*</span></label>
<select name="role" class="fc" required>
<option value="superadmin" {{ old('role',$user->role)==='superadmin'?'selected':'' }}>Super Administrator</option>
<option value="admin" {{ old('role',$user->role)==='admin'?'selected':'' }}>Admin — Scholarship Officer</option>
<option value="officer" {{ old('role',$user->role)==='officer'?'selected':'' }}>Officer — Scholarship Staff</option>
<option value="counselor" {{ old('role',$user->role)==='counselor'?'selected':'' }}>Counselor — Guidance Counseling Staff</option>
<option value="scholarship" {{ old('role',$user->role)==='scholarship'?'selected':'' }}>Scholarship Officer — Scholarship Management Portal</option>
<option value="student" {{ old('role',$user->role)==='student'?'selected':'' }}>Student</option>
</select>
@error('role')<div style="color:var(--danger);font-size:12px;margin-bottom:10px;">{{ $message }}</div>@enderror
<div style="font-size:12px;color:var(--tm);margin-top:6px;">Scholarship Officer accounts will have access to the Scholarship Management Portal only.</div>
</div>
</div></div>

<div style="display:flex;gap:10px;">
<button type="submit" class="btn btn-ac"><i class="fas fa-save"></i> Save Changes</button>
<a href="{{ route('superadmin.users.index') }}" class="btn btn-o">Cancel</a>
@if($user->role!=='superadmin')
<form method="POST" action="{{ route('superadmin.users.toggle',$user->id) }}" style="margin-left:auto;">@csrf @method('PATCH')
<button class="btn {{ $user->is_active?'btn-d':'btn-s' }}" onclick="return confirm('{{ $user->is_active?'Deactivate':'Activate' }} this user?')"><i class="fas fa-{{ $user->is_active?'ban':'check' }}"></i> {{ $user->is_active?'Deactivate':'Activate' }}</button>
</form>
@endif
</div>
</form>
</div>
@endsection
