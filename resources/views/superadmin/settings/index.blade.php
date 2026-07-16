@extends('superadmin.layouts.app')
@section('title','Settings')
@section('page-title','System Settings')
@section('page-sub','Super Admin account and system configuration')
@section('content')
<div style="max-width:740px;">
<div class="card an mb3"><div class="ch"><i class="fas fa-user-shield" style="color:var(--y);"></i><h2>Super Admin Account</h2></div><div class="cb">
<form method="POST" action="{{ route('superadmin.settings.update') }}">@csrf
<div class="g2"><div class="fg"><label class="fl">Full Name</label><input type="text" name="name" class="fc" value="{{ auth()->user()->name }}"></div><div class="fg"><label class="fl">Email Address</label><input type="email" name="email" class="fc" value="{{ auth()->user()->email }}"></div></div>
<div class="g2"><div class="fg"><label class="fl">Current Password</label><input type="password" name="current_password" class="fc" placeholder="Required to change password"></div><div class="fg"><label class="fl">New Password</label><input type="password" name="password" class="fc" placeholder="Min 8 characters"></div></div>
<button type="submit" class="btn btn-ac btn-sm"><i class="fas fa-save"></i> Save Changes</button>
</form></div></div>
<div class="card an mb3"><div class="ch"><i class="fas fa-server" style="color:var(--y);"></i><h2>Database Connection</h2></div><div class="cb">
<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:11px;">
@foreach([['DB Driver',config('database.default')],['DB Host',config('database.connections.'.config('database.default').'.host','local')],['DB Name',config('database.connections.'.config('database.default').'.database','N/A')],['SSL Mode',config('database.connections.'.config('database.default').'.sslmode','N/A')]] as $cfg)
<div style="background:var(--card2);border:1px solid var(--bd);border-radius:var(--rs);padding:11px 13px;"><div style="font-size:11px;color:var(--tm);font-weight:700;text-transform:uppercase;margin-bottom:3px;">{{ $cfg[0] }}</div><div class="mono fwb" style="color:var(--y);">{{ $cfg[1] }}</div></div>
@endforeach
</div>
<div class="alert al-y mt3" style="margin-bottom:0;font-size:12px;"><i class="fas fa-info-circle"></i><span>Edit <code>.env</code> to change DB settings, then run <code>php artisan config:clear</code>.</span></div>
</div></div>
<div class="card an"><div class="ch"><i class="fas fa-info-circle" style="color:var(--y);"></i><h2>System Info</h2></div><div class="cb">
<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:11px;">
@foreach([['PHP Version',PHP_VERSION],['Laravel Version',app()->version()],['App Name',config('app.name')],['Environment',config('app.env')],['App URL',config('app.url')],['Timezone',config('app.timezone')]] as $info)
<div style="background:var(--card2);border:1px solid var(--bd);border-radius:var(--rs);padding:10px 12px;"><div style="font-size:11px;color:var(--tm);margin-bottom:3px;">{{ $info[0] }}</div><div class="mono" style="font-size:13px;color:#fff;">{{ $info[1] }}</div></div>
@endforeach
</div></div></div>
</div>
@endsection
