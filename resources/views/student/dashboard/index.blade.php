@extends('student.layouts.app')
@section('title','Dashboard')
@section('page-title','Dashboard')
@section('page-sub','Welcome back — ISAMS Student Portal')
@section('content')
@php $aiScore = $student ? (function($s){ $apps=\App\Models\ScholarshipApplication::where('student_id',$s->id)->orderByDesc('ai_score')->first(); return $apps?$apps->ai_score:0; })($student) : 0; $eligibility=$aiScore>=75?'Eligible':($aiScore>=50?'For Review':'Not Evaluated'); @endphp
<div style="background:linear-gradient(135deg,#0d5c24,#1a6b2f);border-radius:var(--r);padding:20px 24px;display:flex;align-items:center;gap:18px;margin-bottom:20px;border:1px solid var(--gm);" class="an">
<div style="width:56px;height:56px;border-radius:16px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-size:24px;color:#fff;flex-shrink:0;"><i class="fas fa-{{ $eligibility==='Eligible'?'check-circle':'robot' }}"></i></div>
<div style="flex:1;"><div style="font-family:'Sora',sans-serif;font-size:20px;font-weight:800;color:#fff;">{{ $eligibility==='Eligible'?'You are Eligible!':($eligibility==='For Review'?'Profile Under Review':'Check Your Eligibility') }}</div>
<div style="font-size:13px;color:rgba(255,255,255,.75);margin-top:3px;">GWA: <strong style="color:#fff;">{{ number_format($student?->gwa??0,2) }}</strong> · {{ $student?->enrollment_type??'N/A' }} · {{ Str::limit($student?->course??'N/A',30) }}</div>
<div style="margin-top:10px;"><a href="{{ route('student.eligibility') }}" class="btn btn-ac btn-sm" style="font-size:11px;"><i class="fas fa-robot"></i> View AI Eligibility</a></div></div>
<div style="text-align:center;flex-shrink:0;"><div style="width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,.15);display:flex;flex-direction:column;align-items:center;justify-content:center;"><div style="font-family:'JetBrains Mono',monospace;font-size:22px;font-weight:800;color:#fff;">{{ $aiScore }}%</div><div style="font-size:9px;color:rgba(255,255,255,.7);">AI Score</div></div></div>
</div>
<div class="sg" style="grid-template-columns:repeat(4,1fr);margin-bottom:20px;">
<div class="sc an d1"><div class="si y"><i class="fas fa-award"></i></div><div class="sv"><div class="lbl">Open Programs</div><div class="val">{{ $openScholarships->count() }}</div><div class="chg">Available now</div></div></div>
<div class="sc an d2"><div class="si g"><i class="fas fa-file-alt"></i></div><div class="sv"><div class="lbl">My Applications</div><div class="val">{{ $myApplications->count() }}</div><div class="chg">{{ $myApplications->where('status','Approved')->count() }} approved</div></div></div>
<div class="sc an d3"><div class="si dg"><i class="fas fa-check-circle"></i></div><div class="sv"><div class="lbl">Approved</div><div class="val">{{ $myApplications->where('status','Approved')->count() }}</div><div class="chg">Active grants</div></div></div>
<div class="sc an d4"><div class="si t"><i class="fas fa-heart"></i></div><div class="sv"><div class="lbl">Counseling</div><div class="val">{{ \App\Models\CounselingSession::where('student_id',auth()->user()->student?->id??0)->count() }}</div><div class="chg">Sessions</div></div></div>
</div>
<div class="g2 mb3">
<div class="card an"><div class="ch"><i class="fas fa-award" style="color:var(--yd);"></i><h2>Open Scholarships</h2><div class="ch-acts"><a href="{{ route('student.scholarships') }}" class="btn btn-o btn-sm">Browse All</a></div></div>
<div class="cb" style="padding:0;">
@forelse($openScholarships as $sch)
<div style="padding:13px 18px;border-bottom:1px solid var(--bd);display:flex;align-items:center;gap:11px;">
<div class="si y" style="width:38px;height:38px;font-size:14px;border-radius:10px;flex-shrink:0;"><i class="fas fa-award"></i></div>
<div style="flex:1;"><div class="fws" style="font-size:13px;">{{ $sch->name }}</div><div class="tm" style="font-size:12px;">{{ Str::limit($sch->benefits??'',45) }}</div></div>
<!-- Apply button removed from student dashboard -->
</div>
@empty
<div style="padding:20px;text-align:center;color:var(--tm);">No open scholarships at the moment.</div>
@endforelse
</div></div>
<div>
<div class="card an mb3"><div class="ch"><i class="fas fa-file-alt" style="color:var(--gm);"></i><h2>My Applications</h2><div class="ch-acts"><a href="{{ route('student.applications') }}" class="btn btn-o btn-sm">View All</a></div></div>
<div class="cb" style="padding:0;">
@forelse($myApplications as $app)
<div style="display:flex;align-items:center;gap:11px;padding:12px 18px;border-bottom:1px solid var(--bd);">
<div class="si g" style="width:36px;height:36px;font-size:13px;border-radius:9px;flex-shrink:0;"><i class="fas fa-file-alt"></i></div>
<div style="flex:1;"><div class="fws" style="font-size:13px;">{{ Str::limit($app->scholarship->name??'—',30) }}</div><div class="mono tm" style="font-size:11px;">Score: {{ $app->ai_score }}%</div></div>
<span class="badge {{ $app->status==='Approved'?'b-s':($app->status==='Rejected'?'b-d':'b-w') }}">{{ $app->status }}</span>
</div>
@empty
<div style="padding:18px;text-align:center;color:var(--tm);font-size:13px;">No applications yet. <a href="{{ route('student.scholarships') }}" style="color:var(--gm);">Browse scholarships</a></div>
@endforelse
</div></div>
<div class="card an"><div class="ch"><i class="fas fa-heart" style="color:#e87070;"></i><h2>Counseling</h2><div class="ch-acts"><a href="{{ route('student.counseling.index') }}" class="btn btn-o btn-sm">Manage</a></div></div>
<div class="cb"><div style="background:var(--gp);border-radius:var(--rs);padding:12px 14px;margin-bottom:12px;font-size:13px;color:var(--g);"><i class="fas fa-info-circle" style="margin-right:6px;"></i><strong>Auto-Queue:</strong> All requests are accepted and queued.</div>
<a href="{{ route('student.counseling.index') }}" class="btn btn-p btn-sm" style="width:100%;justify-content:center;"><i class="fas fa-plus"></i> Request Session</a>
</div></div>
</div>
</div>
<div class="card an"><div class="ch"><i class="fas fa-bullhorn" style="color:var(--gm);"></i><h2>Announcements</h2><div class="ch-acts"><a href="{{ route('student.announcements') }}" class="btn btn-o btn-sm">View All</a></div></div>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:12px;padding:18px;">
@forelse($announcements as $ann)
<div style="background:var(--bg);border:1px solid var(--bd);border-radius:var(--rs);padding:13px;transition:all .2s;" onmouseover="this.style.borderColor='var(--gm)'" onmouseout="this.style.borderColor='var(--bd)'">
<div class="fws" style="font-size:13px;margin-bottom:4px;">{{ Str::limit($ann->title,40) }}</div>
<div class="tm" style="font-size:12px;margin-bottom:6px;">{{ Str::limit($ann->body,80) }}</div>
<div style="font-size:11px;color:var(--tm);"><i class="fas fa-clock" style="margin-right:3px;"></i>{{ $ann->created_at->diffForHumans() }}</div>
</div>
@empty
<div style="color:var(--tm);font-size:13px;">No announcements.</div>
@endforelse
</div></div>
@endsection
