@extends('student.layouts.app')
@section('title','My Profile')
@section('page-title','My Profile')
@section('page-sub','Manage your account information')
@section('content')
<div class="g2 an" style="max-width:860px;align-items:start;">
<div>
<div class="card mb3"><div class="ch"><i class="fas fa-user-circle" style="color:var(--gm);"></i><h2>Profile</h2></div><div class="cb" style="text-align:center;">
<div style="width:70px;height:70px;border-radius:50%;background:linear-gradient(135deg,var(--g),var(--gm));display:inline-flex;align-items:center;justify-content:center;font-family:'Sora',sans-serif;font-size:26px;font-weight:800;color:#fff;margin-bottom:10px;">{{ strtoupper(substr(auth()->user()->name??'S',0,1)) }}</div>
<div style="font-family:'Sora',sans-serif;font-size:17px;font-weight:700;color:var(--g);">{{ auth()->user()->name??'Student' }}</div>
<div class="tm" style="font-size:13px;">{{ auth()->user()->email }}</div>
<div style="margin-top:12px;">@foreach([['Student ID',auth()->user()->student?->student_id??'N/A','fas fa-id-card'],['Course',Str::limit(auth()->user()->student?->course??'N/A',30),'fas fa-graduation-cap'],['Year Level',auth()->user()->student?->year_level??'N/A','fas fa-layer-group'],['GWA',number_format(auth()->user()->student?->gwa??0,2),'fas fa-star'],['Enrollment',auth()->user()->student?->enrollment_type??'N/A','fas fa-id-badge']] as $inf)
<div style="display:flex;align-items:center;gap:10px;padding:9px 0;border-bottom:1px solid var(--bd);">
<div style="width:30px;height:30px;background:var(--gp);border-radius:7px;display:flex;align-items:center;justify-content:center;color:var(--gm);flex-shrink:0;"><i class="{{ $inf[2] }}" style="font-size:12px;"></i></div>
<div><div style="font-size:10px;color:var(--tm);font-weight:600;text-transform:uppercase;letter-spacing:.5px;">{{ $inf[0] }}</div><div class="fws" style="font-size:13px;">{{ $inf[1] }}</div></div>
</div>
@endforeach</div>
</div></div>
</div>
<div class="card"><div class="ch"><i class="fas fa-lock" style="color:var(--gm);"></i><h2>Account Settings</h2></div><div class="cb">
<form method="POST" action="{{ route('student.profile.update') }}">@csrf
<div class="fg"><label class="fl">Full Name</label><input type="text" name="name" class="fc" value="{{ auth()->user()->name }}"></div>
<div class="fg"><label class="fl">Email Address</label><input type="email" name="email" class="fc" value="{{ auth()->user()->email }}"></div>
<hr style="border:none;border-top:1px solid var(--bd);margin:16px 0;">
<div style="font-size:13px;font-weight:700;color:var(--g);margin-bottom:12px;">Change Password</div>
<div class="fg"><label class="fl">New Password</label><input type="password" name="password" class="fc" placeholder="Leave blank to keep current"></div>
<div class="fg"><label class="fl">Confirm Password</label><input type="password" name="password_confirmation" class="fc" placeholder="Confirm new password"></div>
<button type="submit" class="btn btn-p btn-sm"><i class="fas fa-save"></i> Save Changes</button>
</form>
</div></div>
</div>
@endsection
