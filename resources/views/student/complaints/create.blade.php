@extends('student.layouts.app')
@section('title','File a Complaint or Report')
@section('page-title','File a Complaint or Report')
@section('page-sub','Submit a complaint or report to the Student Affairs Office')
@section('content')
@if(session('success'))<div class="alert al-s an"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>@endif
@if(session('error'))<div class="alert al-d an"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>@endif
@if($errors->any())<div class="alert al-d an"><i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}</div>@endif
<div class="card an">
<div class="ch"><i class="fas fa-exclamation-circle" style="color:var(--danger);"></i><h2>File a Complaint or Report</h2></div>
<div class="cb">
<form method="POST" action="{{ route('student.complaints.store') }}">
@csrf
<div class="fg"><label class="fl">Type <span style="color:var(--danger);">*</span></label>
<select name="type" class="fc" required>
<option value="Complaint" {{ old('type')==='Complaint'?'selected':'' }}>Complaint</option>
<option value="Report" {{ old('type')==='Report'?'selected':'' }}>Report</option>
</select></div>
<div class="fg"><label class="fl">Subject <span style="color:var(--danger);">*</span></label>
<input type="text" name="subject" class="fc" value="{{ old('subject') }}" required placeholder="Enter a brief subject line for your complaint or report"></div>
<div class="fg"><label class="fl">Description <span style="color:var(--danger);">*</span></label>
<textarea name="description" class="fc" rows="6" required placeholder="Describe your complaint or report in detail. Minimum 20 characters.">{{ old('description') }}</textarea></div>
<div class="fg">
<div style="background:var(--bg);border:1px solid var(--bd);border-left:3px solid var(--y);border-radius:var(--rs);padding:14px;">
<label for="isAnonymous" style="display:flex;align-items:center;gap:9px;cursor:pointer;">
<input type="checkbox" name="is_anonymous" id="isAnonymous" value="1" style="width:16px;height:16px;accent-color:var(--g);">
<span class="fwb" style="font-size:13px;">Remain Anonymous</span>
</label>
<div class="tm" style="font-size:12px;margin-top:7px;line-height:1.55;">If you check this box, your name will not be visible to the admin when they review your complaint. Your complaint will still be processed and taken seriously.</div>
</div></div>
<div style="display:flex;gap:9px;align-items:center;">
<button type="submit" class="btn btn-p"><i class="fas fa-paper-plane"></i> Submit Complaint or Report</button>
<a href="{{ route('student.complaints') }}" class="btn btn-o">Cancel</a>
</div>
</form>
</div>
</div>
@endsection
