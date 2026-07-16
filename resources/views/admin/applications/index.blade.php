@extends('admin.layouts.app')
@section('title','Applications')
@section('page-title','Scholarship Applications')
@section('page-sub','All applications with AI eligibility scores')
@section('content')
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:14px;">
<form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;">
<div style="position:relative;"><i class="fas fa-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--tm);font-size:12px;"></i><input type="text" name="search" value="{{ request('search') }}" class="fc" placeholder="Search student..." style="padding-left:30px;width:180px;"></div>
<select name="status" class="fc" style="width:140px;" onchange="this.form.submit()"><option value="">All Status</option><option value="Pending" {{ request('status')==='Pending'?'selected':'' }}>Pending</option><option value="Approved" {{ request('status')==='Approved'?'selected':'' }}>Approved</option><option value="Rejected" {{ request('status')==='Rejected'?'selected':'' }}>Rejected</option><option value="For Review" {{ request('status')==='For Review'?'selected':'' }}>For Review</option></select>
<select name="ai_eligibility" class="fc" style="width:145px;" onchange="this.form.submit()"><option value="">All AI Results</option><option value="Eligible" {{ request('ai_eligibility')==='Eligible'?'selected':'' }}>Eligible</option><option value="For Review" {{ request('ai_eligibility')==='For Review'?'selected':'' }}>For Review</option><option value="Not Eligible" {{ request('ai_eligibility')==='Not Eligible'?'selected':'' }}>Not Eligible</option></select>
<button type="submit" class="btn btn-p btn-sm"><i class="fas fa-filter"></i> Filter</button>
<a href="{{ route('admin.applications.index') }}" class="btn btn-o btn-sm">Clear</a>
</form>
<div style="display:flex;gap:8px;">
<a href="{{ route('admin.applications.create') }}" class="btn btn-p btn-sm"><i class="fas fa-plus"></i> New Application</a>
<a href="{{ route('admin.ai.index') }}" class="btn btn-ai btn-sm"><i class="fas fa-robot"></i> AI Filter</a>
</div>
</div>
<div class="card an">
<div class="ch"><i class="fas fa-file-alt" style="color:var(--gm);"></i><h2>Applications</h2><span class="badge b-p" style="margin-left:6px;">{{ $applications->total() }}</span></div>
<div class="tw"><table>
<thead><tr><th>Student</th><th>Scholarship</th><th>GWA</th><th>AI Score</th><th>AI Eligibility</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
<tbody>
@forelse($applications as $app)
@php $sc=$app->ai_score??0; $el=$app->ai_eligibility==='Eligible'?'el':($app->ai_eligibility==='For Review'?'rv':'no'); @endphp
<tr>
<td><div style="display:flex;align-items:center;gap:7px;"><div class="av av-s">{{ strtoupper(substr($app->student->first_name??'S',0,1)) }}</div><div><div class="fws" style="font-size:13px;">{{ $app->student->full_name??'—' }}</div><div class="mono tm" style="font-size:10px;">{{ $app->student->student_id??'—' }}</div></div></div></td>
<td style="font-size:12px;color:var(--tm);">{{ Str::limit($app->scholarship->name??'—',22) }}</td>
<td class="mono fwb" style="color:{{ (float)($app->gwa??0)<=1.75?'var(--gm)':'var(--danger)' }}">{{ number_format($app->gwa??0,2) }}</td>
<td style="min-width:90px;"><div style="display:flex;align-items:center;gap:4px;"><div style="flex:1;"><div class="asb"><div class="asf {{ $sc>=75?'ash':($sc>=50?'asm':'asl') }}" style="width:{{ $sc }}%;"></div></div></div><span class="mono" style="font-size:11px;font-weight:700;">{{ $sc }}%</span></div></td>
<td><span class="badge elig-{{ $el }}" style="font-size:10px;">{{ $app->ai_eligibility??'Pending' }}</span></td>
<td><span class="badge {{ $app->status==='Approved'?'b-s':($app->status==='Rejected'?'b-d':'b-w') }}">{{ $app->status }}</span></td>
<td class="mono tm" style="font-size:11px;">{{ $app->created_at->format('M d, Y') }}</td>
<td><div style="display:flex;gap:4px;">
<a href="{{ route('admin.applications.show',$app->id) }}" class="btn btn-o btn-sm btn-ic"><i class="fas fa-eye"></i></a>
@if($app->status==='Pending')
<form method="POST" action="{{ route('admin.applications.updateStatus',$app->id) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="Approved"><button class="btn btn-s btn-sm btn-ic" title="Approve"><i class="fas fa-check"></i></button></form>
<form method="POST" action="{{ route('admin.applications.updateStatus',$app->id) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="Rejected"><button class="btn btn-d btn-sm btn-ic" title="Reject" onclick="return confirm('Reject this application?')"><i class="fas fa-times"></i></button></form>
@endif
</div></td>
</tr>
@empty
<tr><td colspan="8" style="text-align:center;padding:20px;color:var(--tm);">No applications found.</td></tr>
@endforelse
</tbody></table></div>
@if($applications->hasPages())<div style="padding:13px 18px;border-top:1px solid var(--bd);">{{ $applications->links() }}</div>@endif
</div>
@endsection
