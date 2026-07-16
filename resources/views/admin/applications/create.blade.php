@extends('admin.layouts.app')
@section('title','New Application')
@section('page-title','New Application')
@section('content')
<div style="max-width:700px;">
<form method="POST" action="{{ route('admin.applications.store') }}">@csrf
<div class="card an mb3"><div class="ch"><i class="fas fa-file-alt" style="color:var(--gm);"></i><h2>Application Details</h2></div><div class="cb">
<div class="g2">
<div class="fg"><label class="fl">Student <span style="color:var(--danger);">*</span></label><select name="student_id" class="fc" required><option value="">Select student...</option>@foreach($students as $s)<option value="{{ $s->id }}">{{ $s->full_name }} ({{ $s->student_id }})</option>@endforeach</select></div>
<div class="fg"><label class="fl">Scholarship <span style="color:var(--danger);">*</span></label><select name="scholarship_id" class="fc" required><option value="">Select scholarship...</option>@foreach($scholarships as $sch)<option value="{{ $sch->id }}">{{ $sch->name }}</option>@endforeach</select></div>
</div>
<div class="g2">
<div class="fg"><label class="fl">GWA <span style="color:var(--danger);">*</span></label><input type="number" name="gwa" class="fc mono" step="0.01" min="1" max="5" required placeholder="1.75"></div>
<div class="fg"><label class="fl">Enrollment Type</label><select name="enrollment_type" class="fc"><option value="Regular">Regular</option><option value="Irregular">Irregular</option></select></div>
</div>
<div class="g2">
<div class="fg"><label class="fl">Income Bracket</label><select name="income_bracket" class="fc"><option value="below_200">Below ₱200,000</option><option value="200_400">₱200,000–₱400,000</option><option value="above_400">Above ₱400,000</option></select></div>
<div class="fg"><label class="fl">Status</label><select name="status" class="fc"><option value="Pending">Pending</option><option value="Approved">Approved</option><option value="For Review">For Review</option></select></div>
</div>
<div class="g2">
<label style="display:flex;align-items:center;gap:8px;padding:11px;border:1.5px solid var(--bd);border-radius:var(--rs);cursor:pointer;"><input type="checkbox" name="has_failing" value="1" style="accent-color:var(--g);"><span style="font-size:13px;">Has Failing Grades</span></label>
<label style="display:flex;align-items:center;gap:8px;padding:11px;border:1.5px solid var(--bd);border-radius:var(--rs);cursor:pointer;"><input type="checkbox" name="has_discipline" value="1" style="accent-color:var(--g);"><span style="font-size:13px;">Has Discipline Case</span></label>
</div>
<div class="fg"><label class="fl">Remarks</label><textarea name="remarks" class="fc" rows="3" placeholder="Additional notes..."></textarea></div>
<div class="alert al-ai" style="font-size:12px;margin-bottom:0;"><i class="fas fa-robot"></i><span><strong>AI Note:</strong> The AI engine will automatically evaluate this application upon submission.</span></div>
</div></div>
<div style="display:flex;gap:10px;"><button type="submit" class="btn btn-p"><i class="fas fa-paper-plane"></i> Submit Application</button><a href="{{ route('admin.applications.index') }}" class="btn btn-o">Cancel</a></div>
</form></div>
@endsection
