@extends('admin.layouts.app')
@section('title','Dashboard')
@section('page-title','Dashboard')
@section('page-sub','ISAMS — Integrated Student Affairs Management System')
@section('content')
<div class="sg">
<div class="sc an d1"><div class="si y"><i class="fas fa-award"></i></div><div class="sv"><div class="lbl">Active Programs</div><div class="val">{{ $stats['programs'] }}</div><div class="chg">Scholarship programs</div></div></div>
<div class="sc an d2"><div class="si o"><i class="fas fa-hourglass-half"></i></div><div class="sv"><div class="lbl">Pending Apps</div><div class="val">{{ $stats['pending'] }}</div><div class="chg">Awaiting review</div></div></div>
<div class="sc an d3"><div class="si dg"><i class="fas fa-user-graduate"></i></div><div class="sv"><div class="lbl">Active Scholars</div><div class="val">{{ $stats['active_scholars'] }}</div><div class="chg">Approved</div></div></div>
<a href="{{ route('admin.students.index') }}" class="sc an d4" style="text-decoration:none;"><div class="si g"><i class="fas fa-users"></i></div><div class="sv"><div class="lbl">Total Student Accounts</div><div class="val">{{ $totalStudentAccounts }}</div><div class="chg">Registered students</div></div></a>
<a href="{{ route('admin.applications.index') }}" class="sc an d5" style="text-decoration:none;"><div class="si t"><i class="fas fa-graduation-cap"></i></div><div class="sv"><div class="lbl">Students Applied</div><div class="val">{{ $studentsApplied }}</div><div class="chg">Scholarship applicants</div></div></a>
<a href="{{ route('admin.counseling.index') }}" class="sc an d6" style="text-decoration:none;"><div class="si y"><i class="fas fa-comments"></i></div><div class="sv"><div class="lbl">Pending Counseling</div><div class="val">{{ $pendingCounseling }}</div><div class="chg">Requests in queue</div></div></a>
<a href="{{ route('admin.discipline.index') }}" class="sc an d1" style="text-decoration:none;"><div class="si r"><i class="fas fa-gavel"></i></div><div class="sv"><div class="lbl">DSA Discipline Records</div><div class="val">{{ $totalDisciplineCases }}</div><div class="chg">Total cases</div></div></a>
<a href="{{ route('admin.complaints.index') }}" class="sc an d2" style="text-decoration:none;"><div class="si o"><i class="fas fa-exclamation-circle"></i></div><div class="sv"><div class="lbl">Complaints Filed</div><div class="val">{{ $totalComplaints }}</div><div class="chg">From students</div></div></a>
</div>

<div class="g2 mb3" style="align-items:start;">
<div class="card an">
<div class="ch"><i class="fas fa-graduation-cap" style="color:var(--gm);"></i><h2>Recent Scholarship Applications</h2><div class="ch-acts"><a href="{{ route('admin.applications.index') }}" class="btn btn-o btn-sm">View All</a></div></div>
<div class="tw"><table>
<thead><tr><th>Student Name</th><th>Scholarship Name</th><th>Status</th><th>AI Score</th></tr></thead>
<tbody>
@php $recentScholarApps = $recentApps->take(5); @endphp
@forelse($recentScholarApps as $app)
<tr>
<td><div style="display:flex;align-items:center;gap:7px;"><div class="av av-s">{{ strtoupper(substr($app->student->first_name??'S',0,1)) }}</div><div class="fws" style="font-size:13px;">{{ $app->student->full_name??'—' }}</div></div></td>
<td style="font-size:12px;color:var(--tm);">{{ Str::limit($app->scholarship->name??'—',28) }}</td>
<td><span class="badge {{ $app->status==='Approved'?'b-s':($app->status==='Rejected'?'b-d':($app->status==='On Review'?'b-i':($app->status==='Canceled'?'b-gray':'b-w'))) }}">{{ $app->status }}</span></td>
<td class="mono fwb" style="font-size:12px;">{{ $app->ai_score ?? 0 }}%</td>
</tr>
@empty
<tr><td colspan="4" style="text-align:center;padding:28px 20px;color:var(--tm);"><i class="fas fa-graduation-cap" style="font-size:28px;color:var(--bd);display:block;margin-bottom:8px;"></i>No scholarship applications yet.</td></tr>
@endforelse
</tbody></table></div>
</div>
<div class="card an">
<div class="ch"><i class="fas fa-comments" style="color:var(--gm);"></i><h2>Pending Counseling Requests</h2><div class="ch-acts"><a href="{{ route('admin.counseling.index') }}" class="btn btn-o btn-sm">View All</a></div></div>
<div class="tw"><table>
<thead><tr><th>Student Name</th><th>Concern Type</th><th>Priority</th><th>Preferred Date</th><th>Status</th></tr></thead>
<tbody>
@php $pendingSessions = $pendingSessions ?? collect(); @endphp
@forelse($pendingSessions as $s)
<tr>
<td><div style="display:flex;align-items:center;gap:7px;"><div class="av av-s">{{ strtoupper(substr($s->student->first_name??'S',0,1)) }}</div><div class="fws" style="font-size:13px;">{{ $s->student->full_name??'—' }}</div></div></td>
<td style="font-size:12px;">{{ $s->concern_type }}</td>
<td><span class="badge {{ ($s->priority??'')==='Emergency'?'b-d':(($s->priority??'')==='Urgent'?'b-w':'b-gray') }}">{{ $s->priority ?? 'Normal' }}</span></td>
<td class="tm" style="font-size:12px;">{{ $s->preferred_date ? \Illuminate\Support\Carbon::parse($s->preferred_date)->format('M d, Y') : '—' }}</td>
<td><span class="badge b-w">{{ $s->status }}</span></td>
</tr>
@empty
<tr><td colspan="5" style="text-align:center;padding:28px 20px;color:var(--tm);"><i class="fas fa-comments" style="font-size:28px;color:var(--bd);display:block;margin-bottom:8px;"></i>No pending counseling requests.</td></tr>
@endforelse
</tbody></table></div>
</div>
</div>

<div class="card an mb3">
<div class="ch"><i class="fas fa-bolt" style="color:var(--yd);"></i><h2>Quick Actions</h2></div>
<div class="cb" style="display:flex;flex-wrap:wrap;gap:9px;">
<a href="{{ route('admin.applications.create') }}" class="btn btn-p btn-sm"><i class="fas fa-plus"></i> New Application</a>
<a href="{{ route('admin.ai.index') }}" class="btn btn-ai btn-sm"><i class="fas fa-robot"></i> AI Filter</a>
<form method="POST" action="{{ route('admin.ai.run') }}">@csrf<button class="btn btn-ac btn-sm"><i class="fas fa-play"></i> Run AI Scan</button></form>
<a href="{{ route('admin.scholarships.create') }}" class="btn btn-o btn-sm"><i class="fas fa-award"></i> Add Program</a>
<a href="{{ route('admin.students.create') }}" class="btn btn-o btn-sm"><i class="fas fa-user-plus"></i> Add Student</a>
<a href="{{ route('admin.reports.index') }}" class="btn btn-ac btn-sm"><i class="fas fa-chart-bar"></i> Reports</a>
</div></div>
<div class="card an">
<div class="ch"><i class="fas fa-file-alt" style="color:var(--gm);"></i><h2>Recent Applications</h2><div class="ch-acts"><a href="{{ route('admin.applications.index') }}" class="btn btn-o btn-sm">View All</a><a href="{{ route('admin.ai.index') }}" class="btn btn-ai btn-sm"><i class="fas fa-robot"></i> AI Filter</a></div></div>
<div class="tw"><table><thead><tr><th>Student</th><th>Scholarship</th><th>AI Score</th><th>Eligibility</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
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
