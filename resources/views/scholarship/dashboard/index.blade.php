@extends('scholarship.layouts.app')
@section('title','Dashboard')
@section('page-title','Scholarship Dashboard')
@section('page-sub','Scholarship Management Portal')
@section('ai-bar')
<div class="ai-bar"><div class="ai-bar-label"><div class="ai-dot"></div>AI Engine Active</div><div class="ai-bar-stats"><div class="ai-stat">Eligible: <strong>{{ $stats['eligible'] }}</strong></div><div class="ai-stat">For Review: <strong>{{ $stats['for_review'] }}</strong></div><div class="ai-stat">Evaluated Today: <strong>{{ $stats['processed_today'] }}</strong></div><div class="ai-stat">Awaiting Sync: <strong>{{ $stats['synced'] }}</strong></div></div></div>
@endsection
@section('content')
<div class="sg">
<div class="sc an d1"><div class="si g"><i class="fas fa-award"></i></div><div class="sv"><div class="lbl">Active Scholarships</div><div class="val">{{ $stats['programs'] }}</div><div class="chg">Scholarship programs</div></div></div>
<div class="sc an d2"><div class="si y"><i class="fas fa-file-alt"></i></div><div class="sv"><div class="lbl">Pending Applications</div><div class="val">{{ $stats['pending'] }}</div><div class="chg">Awaiting review</div></div></div>
<div class="sc an d3"><div class="si dg"><i class="fas fa-check-circle"></i></div><div class="sv"><div class="lbl">Eligible Applicants</div><div class="val">{{ $stats['eligible'] }}</div><div class="chg"><i class="fas fa-robot" style="font-size:9px;"></i> AI-verified</div></div></div>
<div class="sc an d4"><div class="si o"><i class="fas fa-search"></i></div><div class="sv"><div class="lbl">For Review</div><div class="val">{{ $stats['for_review'] }}</div><div class="chg">Flagged by AI</div></div></div>
<div class="sc an d5"><div class="si t"><i class="fas fa-robot"></i></div><div class="sv"><div class="lbl">AI Evaluations Today</div><div class="val">{{ $stats['processed_today'] }}</div><div class="chg">Run today</div></div></div>
<div class="sc an d6"><div class="si y"><i class="fas fa-flag"></i></div><div class="sv"><div class="lbl">Synced Scholarships</div><div class="val">{{ $stats['synced'] }}</div><div class="chg">Not yet imported</div></div></div>
</div>
<div class="g2 mb3">
<div class="card an"><div class="ch"><i class="fas fa-bolt" style="color:var(--yd);"></i><h2>Quick Actions</h2></div>
<div class="cb" style="display:flex;flex-wrap:wrap;gap:9px;">
<a href="{{ route('scholarship.applications.index') }}" class="btn btn-p btn-sm"><i class="fas fa-file-alt"></i> Applications</a>
<a href="{{ route('scholarship.ai.index') }}" class="btn btn-ai btn-sm"><i class="fas fa-robot"></i> AI Filter</a>
<form method="POST" action="{{ route('scholarship.ai.run') }}">@csrf<button class="btn btn-ac btn-sm"><i class="fas fa-play"></i> Run AI Scan</button></form>
<a href="{{ route('scholarship.programs.create') }}" class="btn btn-o btn-sm"><i class="fas fa-award"></i> Add Program</a>
<a href="{{ route('scholarship.scraper.index') }}" class="btn btn-o btn-sm"><i class="fas fa-flag"></i> PH Scholarship Sync</a>
<a href="{{ route('scholarship.reports.index') }}" class="btn btn-ac btn-sm"><i class="fas fa-chart-bar"></i> Reports</a>
</div></div>
<div class="card an"><div class="ch"><i class="fas fa-robot" style="color:var(--yd);"></i><h2>AI Eligibility Summary</h2><div class="ch-acts"><a href="{{ route('scholarship.ai.index') }}" class="btn btn-ai btn-sm"><i class="fas fa-robot"></i> Full Filter</a></div></div>
<div class="cb">
@foreach([['Eligible',$stats['eligible'],'g','check-circle','ash'],['For Review',$stats['for_review'],'y','exclamation-circle','asm']] as $c)
<div style="display:flex;align-items:center;gap:13px;padding:13px 0;border-bottom:1px solid var(--bd);">
<div class="si {{ $c[2] }}" style="width:42px;height:42px;font-size:16px;border-radius:11px;flex-shrink:0;"><i class="fas fa-{{ $c[3] }}"></i></div>
<div style="flex:1;"><div style="display:flex;justify-content:space-between;margin-bottom:5px;"><span class="fws">{{ $c[0] }}</span><span class="fwb">{{ $c[1] }}</span></div><div class="asb"><div class="asf {{ $c[4] }}" style="width:{{ $stats['eligible'] + $stats['for_review'] > 0 ? round($c[1] / max($stats['eligible'] + $stats['for_review'],1) * 100) : 0 }}%;"></div></div></div>
</div>
@endforeach
<div class="alert al-ai mt3" style="margin-bottom:0;font-size:12px;"><i class="fas fa-lightbulb"></i><span><strong>AI Insight:</strong> Run a fresh AI scan to update all eligibility scores.</span></div>
</div></div>
</div>
<div class="card an">
<div class="ch"><i class="fas fa-file-alt" style="color:var(--gm);"></i><h2>Recent Applications</h2><div class="ch-acts"><a href="{{ route('scholarship.applications.index') }}" class="btn btn-o btn-sm">View All</a><a href="{{ route('scholarship.ai.index') }}" class="btn btn-ai btn-sm"><i class="fas fa-robot"></i> AI Filter</a></div></div>
<div class="tw"><table><thead><tr><th>Student</th><th>Scholarship</th><th>GWA</th><th>AI Score</th><th>Eligibility</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
@php $recentApps = \App\Models\ScholarshipApplication::with(['student','scholarship'])->latest()->take(8)->get(); @endphp
@forelse($recentApps as $app)
@php $sc=$app->ai_score??0; $el=$app->ai_eligibility==='Eligible'?'el':($app->ai_eligibility==='For Review'?'rv':'no'); @endphp
<tr>
<td><div style="display:flex;align-items:center;gap:7px;"><div class="av av-s">{{ strtoupper(substr($app->student->first_name??'S',0,1)) }}</div><div><div class="fws" style="font-size:13px;">{{ $app->student->full_name??'—' }}</div><div class="mono tm" style="font-size:11px;">{{ $app->student->student_id??'—' }}</div></div></div></td>
<td style="font-size:12px;color:var(--tm);">{{ Str::limit($app->scholarship->name??'—',25) }}</td>
<td class="mono fwb" style="color:var(--gm);">{{ number_format($app->gwa??0,2) }}</td>
<td style="min-width:90px;"><div style="display:flex;align-items:center;gap:5px;"><div style="flex:1;"><div class="asb"><div class="asf {{ $sc>=75?'ash':($sc>=50?'asm':'asl') }}" style="width:{{ $sc }}%;"></div></div></div><span class="mono" style="font-size:11px;font-weight:700;">{{ $sc }}%</span></div></td>
<td><span class="badge elig-{{ $el }}" style="font-size:10px;">{{ $app->ai_eligibility??'—' }}</span></td>
<td><span class="badge {{ $app->status==='Approved'?'b-s':($app->status==='Rejected'?'b-d':'b-w') }}">{{ $app->status }}</span></td>
<td><a href="{{ route('scholarship.applications.show',$app->id) }}" class="btn btn-o btn-sm btn-ic"><i class="fas fa-eye"></i></a></td>
</tr>
@empty
<tr><td colspan="7" style="text-align:center;padding:18px;color:var(--tm);">No applications yet. <a href="{{ route('scholarship.applications.index') }}" style="color:var(--gm);">View all</a></td></tr>
@endforelse
</tbody></table></div>
</div>
@endsection
