@extends('admin.layouts.app')
@section('title','Dashboard')
@section('page-title','Dashboard')
@section('page-sub','ISAMS — Integrated Student Affairs Management System')
@section('ai-bar')
<div class="ai-bar"><div class="ai-bar-label"><div class="ai-dot"></div>AI Engine Active</div><div class="ai-bar-stats"><div class="ai-stat">Eligible: <strong>{{ $stats['eligible'] }}</strong></div><div class="ai-stat">For Review: <strong>{{ $stats['for_review'] }}</strong></div><div class="ai-stat">Not Eligible: <strong>{{ $stats['not_eligible'] }}</strong></div><div class="ai-stat">Processed Today: <strong>{{ $stats['processed_today'] }}</strong></div></div></div>
@endsection
@section('content')
<div class="sg">
<div class="sc an d1"><div class="si y"><i class="fas fa-award"></i></div><div class="sv"><div class="lbl">Active Programs</div><div class="val">{{ $stats['programs'] }}</div><div class="chg">Scholarship programs</div></div></div>
<div class="sc an d2"><div class="si g"><i class="fas fa-check-circle"></i></div><div class="sv"><div class="lbl">AI Eligible</div><div class="val">{{ $stats['eligible'] }}</div><div class="chg"><i class="fas fa-robot" style="font-size:9px;"></i> AI-verified</div></div></div>
<div class="sc an d3"><div class="si o"><i class="fas fa-hourglass-half"></i></div><div class="sv"><div class="lbl">Pending Apps</div><div class="val">{{ $stats['pending'] }}</div><div class="chg">Awaiting review</div></div></div>
<div class="sc an d4"><div class="si dg"><i class="fas fa-user-graduate"></i></div><div class="sv"><div class="lbl">Active Scholars</div><div class="val">{{ $stats['active_scholars'] }}</div><div class="chg">Approved</div></div></div>
<div class="sc an d5"><div class="si t"><i class="fas fa-stream"></i></div><div class="sv"><div class="lbl">Counseling Queue</div><div class="val">{{ $stats['counseling_queue'] }}</div><div class="chg">Auto-queued</div></div></div>
<div class="sc an d6"><div class="si r"><i class="fas fa-times-circle"></i></div><div class="sv"><div class="lbl">Not Eligible</div><div class="val">{{ $stats['not_eligible'] }}</div><div class="chg dn">Needs attention</div></div></div>
</div>
<div class="g2 mb3">
<div class="card an"><div class="ch"><i class="fas fa-robot" style="color:var(--yd);"></i><h2>AI Eligibility Summary</h2><div class="ch-acts"><a href="{{ route('admin.ai.index') }}" class="btn btn-ai btn-sm"><i class="fas fa-robot"></i> Full Filter</a></div></div>
<div class="cb">
@foreach([['Eligible',$stats['eligible'],'68%','g','check-circle','ash'],['For Review',$stats['for_review'],'19%','y','exclamation-circle','asm'],['Not Eligible',$stats['not_eligible'],'13%','r','times-circle','asl']] as $c)
<div style="display:flex;align-items:center;gap:13px;padding:13px 0;border-bottom:1px solid var(--bd);">
<div class="si {{ $c[3] }}" style="width:42px;height:42px;font-size:16px;border-radius:11px;flex-shrink:0;"><i class="fas fa-{{ $c[4] }}"></i></div>
<div style="flex:1;"><div style="display:flex;justify-content:space-between;margin-bottom:5px;"><span class="fws">{{ $c[0] }}</span><span class="fwb">{{ $c[1] }} <span class="tm" style="font-weight:400;font-size:11px;">({{ $c[2] }})</span></span></div><div class="asb"><div class="asf {{ $c[5] }}" style="width:{{ $c[2] }};"></div></div></div>
</div>
@endforeach
<div class="alert al-ai mt3" style="margin-bottom:0;font-size:12px;"><i class="fas fa-lightbulb"></i><span><strong>AI Insight:</strong> Run a fresh AI scan to update all eligibility scores.</span></div>
</div></div>
<div class="card an"><div class="ch"><i class="fas fa-bolt" style="color:var(--yd);"></i><h2>Quick Actions</h2></div>
<div class="cb" style="display:flex;flex-wrap:wrap;gap:9px;">
<a href="{{ route('admin.applications.create') }}" class="btn btn-p btn-sm"><i class="fas fa-plus"></i> New Application</a>
<a href="{{ route('admin.ai.index') }}" class="btn btn-ai btn-sm"><i class="fas fa-robot"></i> AI Filter</a>
<form method="POST" action="{{ route('admin.ai.run') }}">@csrf<button class="btn btn-ac btn-sm"><i class="fas fa-play"></i> Run AI Scan</button></form>
<a href="{{ route('admin.scholarships.create') }}" class="btn btn-o btn-sm"><i class="fas fa-award"></i> Add Program</a>
<a href="{{ route('admin.students.create') }}" class="btn btn-o btn-sm"><i class="fas fa-user-plus"></i> Add Student</a>
<a href="{{ route('admin.counseling.index') }}" class="btn btn-o btn-sm"><i class="fas fa-heart"></i> Counseling Queue</a>
<a href="{{ route('admin.reports.index') }}" class="btn btn-ac btn-sm"><i class="fas fa-chart-bar"></i> Reports</a>
<a href="{{ route('admin.announcements.create') }}" class="btn btn-o btn-sm"><i class="fas fa-bullhorn"></i> Announce</a>
</div></div>
</div>
<div class="card an">
<div class="ch"><i class="fas fa-file-alt" style="color:var(--gm);"></i><h2>Recent Applications</h2><div class="ch-acts"><a href="{{ route('admin.applications.index') }}" class="btn btn-o btn-sm">View All</a><a href="{{ route('admin.ai.index') }}" class="btn btn-ai btn-sm"><i class="fas fa-robot"></i> AI Filter</a></div></div>
<div class="tw"><table><thead><tr><th>Student</th><th>Scholarship</th><th>AI Score</th><th>Eligibility</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
@php $recentApps = \App\Models\ScholarshipApplication::with(['student','scholarship'])->latest()->take(8)->get(); @endphp
@forelse($recentApps as $app)
@php $sc=$app->ai_score??0; $el=$app->ai_eligibility==='Eligible'?'el':($app->ai_eligibility==='For Review'?'rv':'no'); @endphp
<tr>
<td><div style="display:flex;align-items:center;gap:7px;"><div class="av av-s">{{ strtoupper(substr($app->student->first_name??'S',0,1)) }}</div><div><div class="fws" style="font-size:13px;">{{ $app->student->full_name??'—' }}</div><div class="mono tm" style="font-size:11px;">{{ $app->student->student_id??'—' }}</div></div></div></td>
<td style="font-size:12px;color:var(--tm);">{{ Str::limit($app->scholarship->name??'—',25) }}</td>
<td style="min-width:90px;"><div style="display:flex;align-items:center;gap:5px;"><div style="flex:1;"><div class="asb"><div class="asf {{ $sc>=75?'ash':($sc>=50?'asm':'asl') }}" style="width:{{ $sc }}%;"></div></div></div><span class="mono" style="font-size:11px;font-weight:700;">{{ $sc }}%</span></div></td>
<td><span class="badge elig-{{ $el }}" style="font-size:10px;">{{ $app->ai_eligibility??'—' }}</span></td>
<td><span class="badge {{ $app->status==='Approved'?'b-s':($app->status==='Rejected'?'b-d':'b-w') }}">{{ $app->status }}</span></td>
<td><a href="{{ route('admin.applications.show',$app->id) }}" class="btn btn-o btn-sm btn-ic"><i class="fas fa-eye"></i></a></td>
</tr>
@empty
<tr><td colspan="6" style="text-align:center;padding:18px;color:var(--tm);">No applications yet. <a href="{{ route('admin.applications.create') }}" style="color:var(--gm);">Add one</a></td></tr>
@endforelse
</tbody></table></div>
</div>
@endsection
