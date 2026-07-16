@extends('student.layouts.app')
@section('title','Application Details')
@section('page-title','Application Details')
@section('page-sub','{{ $application->scholarship->name ?? "Scholarship Application" }}')
@section('content')
<div style="max-width:700px;">
<div style="margin-bottom:14px;"><a href="{{ route('student.applications') }}" class="btn btn-o btn-sm"><i class="fas fa-arrow-left"></i> Back</a></div>
@php $sc=$application->ai_score??0; $el=$application->ai_eligibility==='Eligible'?'eligible':($application->ai_eligibility==='For Review'?'review':'not'); @endphp
<div class="elig-banner {{ $el }} an">
<div class="elig-icon" style="font-size:24px;color:#fff;"><i class="fas fa-{{ $el==='eligible'?'check-circle':($el==='review'?'exclamation-circle':'times-circle') }}"></i></div>
<div style="flex:1;"><div class="elig-title">{{ $application->ai_eligibility??'Pending' }}</div><div class="elig-sub">{{ $application->scholarship->name }}</div><div style="margin-top:8px;"><span class="tag tag-{{ $application->ai_tag==='Renewal Ready'?'renewal':($application->ai_tag==='Needs Requirements'?'needs':'disq') }}">{{ $application->ai_tag??'—' }}</span></div></div>
<div class="chance-circle"><div><div class="chance-val">{{ $sc }}%</div><div class="chance-lbl">AI Score</div></div></div>
</div>
<div class="card an mt3 mb3"><div class="ch"><i class="fas fa-file-alt" style="color:var(--gm);"></i><h2>Application Details</h2><div class="ch-acts"><span class="badge {{ $application->status==='Approved'?'b-s':($application->status==='Rejected'?'b-d':'b-w') }}">{{ $application->status }}</span></div></div>
<div class="cb">
<div class="g2 mb3">
@foreach([['GWA',number_format($application->gwa,2),'fas fa-star'],['Enrollment',$application->enrollment_type,'fas fa-id-badge'],['Has Failing',$application->has_failing?'Yes':'No','fas fa-exclamation'],['Has Discipline',$application->has_discipline?'Yes':'No','fas fa-gavel'],['Income Bracket',$application->income_bracket,'fas fa-hand-holding-usd'],['Filed',\Carbon\Carbon::parse($application->created_at)->format('M d, Y'),'fas fa-calendar']] as $d)
<div style="background:var(--bg);border:1px solid var(--bd);border-radius:var(--rs);padding:11px 13px;"><div style="font-size:11px;color:var(--tm);font-weight:600;margin-bottom:3px;"><i class="{{ $d[2] }}" style="margin-right:4px;"></i>{{ $d[0] }}</div><div class="fws" style="font-size:14px;">{{ $d[1] }}</div></div>
@endforeach
</div>
@if($application->ai_reasoning)<div class="alert al-ai" style="margin-bottom:0;font-size:12px;"><i class="fas fa-robot"></i><span><strong>AI Reasoning:</strong> {{ $application->ai_reasoning }}</span></div>@endif
</div></div>
</div>
@endsection
