@extends('admin.layouts.app')
@section('title','Edit Student')
@section('page-title','Edit Student')
@section('content')
<div style="max-width:700px;">
<div style="margin-bottom:14px;"><a href="{{ route('admin.students.show',$student->id) }}" class="btn btn-o btn-sm"><i class="fas fa-arrow-left"></i> Back</a></div>
<form method="POST" action="{{ route('admin.students.update',$student->id) }}">@csrf @method('PUT')
<div class="card an mb3"><div class="ch"><i class="fas fa-user-graduate" style="color:var(--gm);"></i><h2>Edit: {{ $student->full_name }}</h2></div><div class="cb">
<div class="g2">
<div class="fg"><label class="fl">First Name</label><input type="text" name="first_name" class="fc" value="{{ $student->first_name }}" required></div>
<div class="fg"><label class="fl">Last Name</label><input type="text" name="last_name" class="fc" value="{{ $student->last_name }}" required></div>
</div>
<div class="g2">
<div class="fg"><label class="fl">GWA</label><input type="number" name="gwa" class="fc mono" step="0.01" min="1" max="5" value="{{ $student->gwa }}"></div>
<div class="fg"><label class="fl">Enrollment Type</label><select name="enrollment_type" class="fc"><option value="Regular" {{ $student->enrollment_type==='Regular'?'selected':'' }}>Regular</option><option value="Irregular" {{ $student->enrollment_type==='Irregular'?'selected':'' }}>Irregular</option></select></div>
</div>
<div class="g2">
<div class="fg"><label class="fl">Course</label><select name="course" class="fc">@foreach(['BS Computer Science','BS Information Technology','BS Engineering','BS Education','BS Nursing','BS Business Administration','AB Communication','BS Psychology','BS Accountancy','BS Tourism'] as $c)<option value="{{ $c }}" {{ $student->course===$c?'selected':'' }}>{{ $c }}</option>@endforeach</select></div>
<div class="fg"><label class="fl">Year Level</label><select name="year_level" class="fc">@foreach(['1st Year','2nd Year','3rd Year','4th Year'] as $y)<option value="{{ $y }}" {{ $student->year_level===$y?'selected':'' }}>{{ $y }}</option>@endforeach</select></div>
</div>
<div class="g2">
<div class="fg"><label class="fl">Status</label><select name="status" class="fc"><option value="Active" {{ $student->status==='Active'?'selected':'' }}>Active</option><option value="Inactive" {{ $student->status==='Inactive'?'selected':'' }}>Inactive</option></select></div>
<div class="fg"><label class="fl">Contact</label><input type="text" name="contact_number" class="fc" value="{{ $student->contact_number }}"></div>
</div>
</div></div>
<div style="display:flex;gap:10px;"><button type="submit" class="btn btn-p"><i class="fas fa-save"></i> Update</button><a href="{{ route('admin.students.index') }}" class="btn btn-o">Cancel</a></div>
</form></div>
@endsection
