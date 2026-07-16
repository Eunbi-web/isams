@extends('admin.layouts.app')
@section('title','Application')
@section('page-title','Application Details')
@section('content')
<div style="max-width:760px;">
<div style="margin-bottom:14px;display:flex;gap:8px;"><a href="{{ route('admin.applications.index') }}" class="btn btn-o btn-sm"><i class="fas fa-arrow-left"></i> Back</a></div>
@php $sc=$application->ai_score??0; $el=$application->ai_eligibility==='Eligible'?'eligible':($application->ai_eligibility==='For Review'?'review':'not'); @endphp
<div class="elig-banner {{ $el }} an">
<div class="elig-icon" style="font-size:24px;color:#fff;"><i class="fas fa-{{ $el==='eligible'?'check-circle':($el==='review'?'exclamation-circle':'times-circle') }}"></i></div>
<div style="flex:1;"><div class="elig-title">{{ $application->ai_eligibility??'Pending' }}</div><div class="elig-sub">{{ $application->scholarship->name??'—' }}</div><div class="tm" style="font-size:12px;margin-top:4px;">Student: {{ $application->student->full_name??'—' }} · {{ $application->student->student_id??'—' }}</div></div>
<div class="chance-circle"><div><div class="chance-val">{{ $sc }}%</div><div class="chance-lbl">AI Score</div></div></div>
</div>
<div class="g2 mt3">
<div class="card an"><div class="ch"><i class="fas fa-file-alt" style="color:var(--gm);"></i><h2>Details</h2><div class="ch-acts"><span class="badge {{ $application->status==='Approved'?'b-s':($application->status==='Rejected'?'b-d':'b-w') }}">{{ $application->status }}</span></div></div><div class="cb">
@foreach([['GWA',number_format($application->gwa??0,2)],['Enrollment',$application->enrollment_type],['Failing',$application->has_failing?'Yes':'No'],['Discipline',$application->has_discipline?'Yes':'No'],['Income',$application->income_bracket],['Filed',$application->created_at->format('M d, Y')]] as $d)
<div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--bd);font-size:13px;"><span class="tm">{{ $d[0] }}</span><span class="fws">{{ $d[1] }}</span></div>
@endforeach
</div></div>
<div class="card an"><div class="ch"><i class="fas fa-robot" style="color:var(--yd);"></i><h2>AI Analysis</h2></div><div class="cb">
<div style="text-align:center;margin-bottom:14px;"><div style="width:70px;height:70px;border-radius:50%;background:{{ $sc>=75?'linear-gradient(135deg,var(--g),var(--gm))':($sc>=50?'linear-gradient(135deg,var(--warn),#f0b429)':'linear-gradient(135deg,var(--danger),#e87070)') }};display:inline-flex;align-items:center;justify-content:center;font-family:'JetBrains Mono',monospace;font-size:20px;font-weight:800;color:#fff;">{{ $sc }}%</div></div>
<div class="asb mb2"><div class="asf {{ $sc>=75?'ash':($sc>=50?'asm':'asl') }}" style="width:{{ $sc }}%;"></div></div>
@if($application->ai_tag)<div style="text-align:center;margin-bottom:12px;"><span class="tag tag-{{ $application->ai_tag==='Renewal Ready'?'renewal':($application->ai_tag==='Needs Requirements'?'needs':'disq') }}">{{ $application->ai_tag }}</span></div>@endif
@if($application->ai_reasoning)<div class="alert al-ai" style="font-size:12px;margin-bottom:12px;"><i class="fas fa-robot"></i><span>{{ $application->ai_reasoning }}</span></div>@endif
@if($application->ai_run_at)<div style="font-size:11px;color:var(--tm);text-align:center;"><i class="fas fa-clock" style="margin-right:4px;"></i>AI run: {{ $application->ai_run_at->diffForHumans() }}</div>@endif
</div></div>
</div>
@if($application->status==='Pending')
<div class="card an mt3"><div class="ch"><i class="fas fa-gavel" style="color:var(--gm);"></i><h2>Update Status</h2></div><div class="cb" style="display:flex;gap:10px;">
<form method="POST" action="{{ route('admin.applications.updateStatus',$application->id) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="Approved"><button class="btn btn-s"><i class="fas fa-check"></i> Approve Application</button></form>
<form method="POST" action="{{ route('admin.applications.updateStatus',$application->id) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="Rejected"><button class="btn btn-d" onclick="return confirm('Reject this application?')"><i class="fas fa-times"></i> Reject</button></form>
<form method="POST" action="{{ route('admin.applications.updateStatus',$application->id) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="For Review"><button class="btn btn-o"><i class="fas fa-search"></i> Mark For Review</button></form>
</div></div>
@endif
</div>
@endsection
