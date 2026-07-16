@extends('student.layouts.app')
@section('title','Apply')
@section('page-title','Apply for Scholarship')
@section('page-sub','{{ $scholarship->name ?? "Scholarship" }}')
@section('content')
<div style="max-width:700px;">
<div style="margin-bottom:14px;"><a href="{{ route('student.scholarships') }}" class="btn btn-o btn-sm"><i class="fas fa-arrow-left"></i> Back</a></div>
<div class="card an mb3"><div class="ch"><div class="si y" style="width:40px;height:40px;font-size:16px;border-radius:10px;flex-shrink:0;"><i class="fas fa-award"></i></div><div><h2>{{ $scholarship->name }}</h2><div class="tm" style="font-size:12px;">{{ $scholarship->type }} — {{ $scholarship->benefits }}</div></div></div></div>
<form method="POST" action="{{ route('student.applications.store') }}">@csrf
<input type="hidden" name="scholarship_id" value="{{ $scholarship->id }}">
<div class="card an mb3"><div class="ch"><i class="fas fa-robot" style="color:var(--yd);"></i><h2>Academic Information</h2><span style="font-size:12px;color:var(--tm);margin-left:6px;">Used for AI evaluation</span></div><div class="cb">
<div class="g2">
<div class="fg"><label class="fl">Current GWA <span style="color:var(--danger);">*</span></label><input type="number" name="gwa" class="fc mono" step="0.01" min="1.00" max="5.00" placeholder="1.75" required value="{{ auth()->user()->student?->gwa }}"></div>
<div class="fg"><label class="fl">Enrollment Type <span style="color:var(--danger);">*</span></label><select name="enrollment_type" class="fc" required><option value="Regular" {{ (auth()->user()->student?->enrollment_type)==='Regular'?'selected':'' }}>Regular</option><option value="Irregular" {{ (auth()->user()->student?->enrollment_type)==='Irregular'?'selected':'' }}>Irregular</option></select></div>
</div>
<div class="g2">
<div class="fg"><label class="fl">Income Bracket</label><select name="income_bracket" class="fc"><option value="below_200">Below ₱200,000/year</option><option value="200_400">₱200,000–₱400,000/year</option><option value="above_400">Above ₱400,000/year</option></select></div>
<div></div>
</div>
<div class="g2">
<label style="display:flex;align-items:center;gap:8px;padding:11px 13px;border:1.5px solid var(--bd);border-radius:var(--rs);cursor:pointer;"><input type="checkbox" name="has_failing" value="1" style="accent-color:var(--g);"><span style="font-size:13px;">Has failing grades last semester</span></label>
<label style="display:flex;align-items:center;gap:8px;padding:11px 13px;border:1.5px solid var(--bd);border-radius:var(--rs);cursor:pointer;"><input type="checkbox" name="has_discipline" value="1" style="accent-color:var(--g);"><span style="font-size:13px;">Has active discipline case</span></label>
</div>
</div></div>
<div class="card an mb3"><div class="ch"><i class="fas fa-pen" style="color:var(--gm);"></i><h2>Application Details</h2></div><div class="cb">
<div class="fg"><label class="fl">Why are you applying? (Optional)</label><textarea name="essay" class="fc" rows="4" placeholder="Briefly describe your need for this scholarship and how it will help you..."></textarea></div>
<div class="fg"><label class="fl">Additional Notes</label><textarea name="remarks" class="fc" rows="2" placeholder="Any additional information..."></textarea></div>
</div></div>
<div class="alert al-ai an" style="font-size:12px;"><i class="fas fa-robot"></i><span><strong>AI Note:</strong> Your application will be automatically evaluated by our AI engine upon submission. You will receive your AI eligibility score and reasoning instantly.</span></div>
<div style="display:flex;gap:10px;margin-top:14px;">
<button type="submit" class="btn btn-p"><i class="fas fa-paper-plane"></i> Submit Application</button>
<a href="{{ route('student.scholarships') }}" class="btn btn-o">Cancel</a>
</div>
</form>
</div>
@endsection
