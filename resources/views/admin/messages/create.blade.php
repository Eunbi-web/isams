@extends('admin.layouts.app')
@section('title','Send Message to Student')
@section('page-title','Send Message to Student')
@section('page-sub','One-way message that the student can read in their portal')
@section('content')
<div class="card an">
<div class="ch"><i class="fas fa-paper-plane" style="color:var(--gm);"></i><h2>Send Message to Student</h2></div>
<div class="cb">
<form method="POST" action="{{ route('admin.messages.store') }}">
@csrf
<div class="fg"><label class="fl">Recipient <span style="color:var(--danger);">*</span></label>
<select name="student_id" class="fc" required>
<option value="">Select a student...</option>
@foreach($students as $s)
<option value="{{ $s->id }}" {{ old('student_id')==$s->id?'selected':'' }}>{{ $s->student_id }} - {{ $s->last_name }}, {{ $s->first_name }}</option>
@endforeach
</select></div>
<div class="fg"><label class="fl">Subject <span style="color:var(--danger);">*</span></label>
<input type="text" name="subject" class="fc" value="{{ old('subject') }}" required placeholder="Enter the message subject"></div>
<div class="fg"><label class="fl">Message Body <span style="color:var(--danger);">*</span></label>
<textarea name="body" class="fc" rows="8" required placeholder="Write your message to the student here...">{{ old('body') }}</textarea></div>
<div style="display:flex;gap:9px;align-items:center;">
<button type="submit" class="btn btn-p"><i class="fas fa-paper-plane"></i> Send Message</button>
<a href="{{ route('admin.messages.index') }}" class="btn btn-o">Cancel</a>
</div>
</form>
</div>
</div>
@endsection
