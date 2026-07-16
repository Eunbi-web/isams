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
<div class="alert al-y" style="margin-bottom:14px;font-size:12px;"><i class="fas fa-exclamation-triangle"></i><span>Current role: <strong>{{ ucfirst($user->role) }}</strong>. Changing role will change what portals this user can access.</span></div>
<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;">
@foreach([['student','Student','fas fa-user-graduate'],['officer','Officer','fas fa-user-tie'],['admin','Admin','fas fa-user-shield'],['superadmin','Super Admin','fas fa-crown']] as $role)
<label style="display:flex;align-items:center;gap:10px;padding:11px;border:2px solid {{ old('role',$user->role)===$role[0]?'var(--y)':'var(--bd)' }};border-radius:var(--rs);cursor:pointer;transition:all .2s;background:var(--card2);">
<input type="radio" name="role" value="{{ $role[0] }}" {{ old('role',$user->role)===$role[0]?'checked':'' }} style="accent-color:var(--y);" onchange="document.querySelectorAll('.rol').forEach(l=>l.closest('label').style.borderColor='var(--bd)');this.closest('label').style.borderColor='var(--y)'">
<i class="{{ $role[2] }}" style="color:var(--y);font-size:14px;"></i><span class="fws rol" style="color:#fff;font-size:13px;">{{ $role[1] }}</span>
</label>
@endforeach
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
