@extends('admin.layouts.app')
@section('title','Settings')
@section('page-title','Settings')
@section('content')
<div style="max-width:680px;">
<div class="card an mb3"><div class="ch"><i class="fas fa-user" style="color:var(--gm);"></i><h2>Account Settings</h2></div><div class="cb">
<form method="POST" action="{{ route('admin.settings.update') }}">@csrf
<div class="g2">
<div class="fg"><label class="fl">Full Name</label><input type="text" name="name" class="fc" value="{{ auth()->user()->name }}"></div>
<div class="fg"><label class="fl">Email</label><input type="email" name="email" class="fc" value="{{ auth()->user()->email }}"></div>
</div>
<div class="g2">
<div class="fg"><label class="fl">Current Password</label><input type="password" name="current_password" class="fc" placeholder="To change password"></div>
<div class="fg"><label class="fl">New Password</label><input type="password" name="password" class="fc" placeholder="Min 8 characters"></div>
</div>
<button type="submit" class="btn btn-p btn-sm"><i class="fas fa-save"></i> Save</button>
</form></div></div>
</div>
@endsection
