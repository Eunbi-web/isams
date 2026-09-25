@extends('scholarship.layouts.app')
@section('title','Settings')
@section('page-title','Settings')
@section('page-sub','Manage your account and preferences')
@section('content')
<div style="max-width:680px;">

{{-- Appearance --}}
<div class="card an mb3" style="border:2px solid var(--y);">
<div class="ch" style="background:linear-gradient(135deg,#0d3318,#1a6b2f);gap:12px;">
<div class="si y" style="width:42px;height:42px;border-radius:12px;font-size:17px;"><i class="fas fa-palette"></i></div>
<div><h2 style="color:#fff;">Appearance</h2><div style="font-size:12px;color:rgba(255,255,255,.65);">Choose how ISAMS looks on your device</div></div>
</div>
<div class="cb">
<label class="fl">Color Theme</label>
<div style="display:flex;gap:10px;flex-wrap:wrap;margin:8px 0 14px;">
<button type="button" data-theme-btn="light" onclick="isamsSetTheme('light')" class="btn {{ (auth()->user()->theme ?? 'system')==='light'?'btn-p':'btn-o' }}"><i class="fas fa-sun"></i> Light</button>
<button type="button" data-theme-btn="dark" onclick="isamsSetTheme('dark')" class="btn {{ (auth()->user()->theme ?? 'system')==='dark'?'btn-p':'btn-o' }}"><i class="fas fa-moon"></i> Dark</button>
<button type="button" data-theme-btn="system" onclick="isamsSetTheme('system')" class="btn {{ (auth()->user()->theme ?? 'system')==='system'?'btn-p':'btn-o' }}"><i class="fas fa-circle-half-stroke"></i> System</button>
</div>
<div style="border-radius:var(--rs);border:1px solid var(--bd);overflow:hidden;">
<div style="height:54px;background:linear-gradient(135deg,#0d3318,#1a6b2f);display:flex;align-items:center;padding:0 14px;gap:8px;"><span style="width:9px;height:9px;border-radius:50%;background:var(--y);display:inline-block;"></span><span style="font-size:12px;color:#fff;font-weight:600;">Preview</span></div>
<div style="padding:12px 14px;background:var(--bg);"><span class="badge b-s">Active</span> <span class="badge b-gray">Preview</span> <span style="font-size:12px;color:var(--tm);">Theme applies instantly and is saved to your account.</span></div>
</div>
</div>
</div>

{{-- Account Settings --}}
<div class="card an mb3">
<div class="ch"><i class="fas fa-user-circle" style="color:var(--gm);"></i><h2>Account Settings</h2></div>
<div class="cb">
@if(session('success'))<div class="alert al-s"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>@endif
@if(session('error'))<div class="alert al-d"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>@endif
<form method="POST" action="{{ route('scholarship.settings.update') }}">@csrf
<div class="g2">
<div class="fg"><label class="fl">Full Name <span style="color:var(--danger)">*</span></label><input type="text" name="name" class="fc" value="{{ old('name', auth()->user()->name) }}" required></div>
<div class="fg"><label class="fl">Email Address <span style="color:var(--danger)">*</span></label><input type="email" name="email" class="fc" value="{{ old('email', auth()->user()->email) }}" required></div>
</div>
<div class="g2">
<div class="fg"><label class="fl">Contact Number</label><input type="text" name="contact_number" class="fc" value="{{ old('contact_number', auth()->user()->contact_number) }}" placeholder="09xx xxx xxxx"></div>
<div class="fg"><label class="fl">Department / Office</label><input type="text" name="department" class="fc" value="{{ old('department', auth()->user()->department) }}" placeholder="e.g. Student Affairs"></div>
</div>
<div class="g2">
<div class="fg"><label class="fl">Current Password</label><input type="password" name="current_password" class="fc" placeholder="To change password"></div>
<div class="fg"><label class="fl">New Password</label><input type="password" name="password" class="fc" placeholder="Min 8 characters"></div>
</div>
<div class="fg"><label class="fl">Confirm New Password</label><input type="password" name="password_confirmation" class="fc" placeholder="Repeat new password"></div>
<button type="submit" class="btn btn-p"><i class="fas fa-save"></i> Save Changes</button>
</form>
</div>
</div>

{{-- Account Overview --}}
<div class="card an">
<div class="ch"><i class="fas fa-id-badge" style="color:var(--info);"></i><h2>Account Overview</h2></div>
<div class="cb">
<div class="g2">
<div style="background:var(--bg);border:1px solid var(--bd);border-radius:var(--rs);padding:12px;"><div style="font-size:11px;color:var(--tm);margin-bottom:3px;"><i class="fas fa-user-shield" style="margin-right:4px;"></i>Role</div><div class="fws" style="font-size:14px;text-transform:capitalize;">{{ ucfirst(auth()->user()->role ?? 'admin') }}</div></div>
<div style="background:var(--bg);border:1px solid var(--bd);border-radius:var(--rs);padding:12px;"><div style="font-size:11px;color:var(--tm);margin-bottom:3px;"><i class="fas fa-envelope" style="margin-right:4px;"></i>Account Email</div><div class="fws" style="font-size:14px;">{{ auth()->user()->email }}</div></div>
<div style="background:var(--bg);border:1px solid var(--bd);border-radius:var(--rs);padding:12px;"><div style="font-size:11px;color:var(--tm);margin-bottom:3px;"><i class="fas fa-sign-in-alt" style="margin-right:4px;"></i>Last Login</div><div class="fws" style="font-size:14px;">{{ optional(auth()->user()->last_login_at)->format('M d, Y h:i A') ?? '—' }}</div></div>
<div style="background:var(--bg);border:1px solid var(--bd);border-radius:var(--rs);padding:12px;"><div style="font-size:11px;color:var(--tm);margin-bottom:3px;"><i class="fas fa-check-circle" style="margin-right:4px;"></i>Account Status</div><div class="fws" style="font-size:14px;">{{ (auth()->user()->is_active ?? true) ? 'Active' : 'Inactive' }}</div></div>
</div>
</div>
</div>
</div>
@endsection
@push('scripts')
<script>
function isamsSetTheme(mode){
    var resolved=mode;
    if(mode==='system'){
        var prefersDark=window.matchMedia&&window.matchMedia('(prefers-color-scheme: dark)').matches;
        resolved=prefersDark?'dark':'light';
    }
    document.body.setAttribute('data-theme',resolved);
    document.querySelectorAll('[data-theme-btn]').forEach(function(b){
        var on=b.getAttribute('data-theme-btn')===mode;
        b.className='btn '+(on?'btn-p':'btn-o');
    });
    var csrf=document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch('{{ route('settings.theme.update') }}',{
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'X-Requested-With':'XMLHttpRequest'},
        body:JSON.stringify({theme:mode})
    }).catch(function(){});
}
</script>
@endpush
