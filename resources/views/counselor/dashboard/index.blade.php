@extends('counselor.layouts.app')
@section('title','Dashboard')
@section('page-title','Counselor Dashboard')
@section('page-sub','Guidance Counseling Staff Portal')
@section('content')
<div class="sg">
<div class="sc an d1"><div class="si t"><i class="fas fa-clock"></i></div><div class="sv"><div class="lbl">Counseling In Queue</div><div class="val">{{ $inQueue }}</div><div class="chg">Awaiting schedule</div></div></div>
<div class="sc an d2"><div class="si g"><i class="fas fa-calendar-check"></i></div><div class="sv"><div class="lbl">Scheduled Sessions</div><div class="val">{{ $scheduled }}</div><div class="chg">Upcoming</div></div></div>
<div class="sc an d3"><div class="si dg"><i class="fas fa-check-circle"></i></div><div class="sv"><div class="lbl">Completed Sessions</div><div class="val">{{ $completed }}</div><div class="chg">Finished</div></div></div>
<div class="sc an d4"><div class="si y"><i class="fas fa-bullhorn"></i></div><div class="sv"><div class="lbl">Total Announcements</div><div class="val">{{ $totalAnnouncements }}</div><div class="chg">Published</div></div></div>
</div>

<div class="card an">
<div class="ch"><i class="fas fa-comments" style="color:var(--gm);"></i><h2>Recent Counseling Requests</h2><div class="ch-acts"><a href="{{ route('counselor.counseling.index') }}" class="btn btn-o btn-sm">View All</a></div></div>
<div class="tw"><table>
<thead><tr><th>Student Name</th><th>Concern Type</th><th>Priority</th><th>Preferred Date</th><th>Status</th><th>Action</th></tr></thead>
<tbody>
@forelse($recent as $session)
<tr {{ $session->status==='In Queue'?'style="background:rgba(26,107,47,0.04);"':'' }}>
<td><div style="display:flex;align-items:center;gap:7px;"><div class="av av-s">{{ strtoupper(substr($session->student->first_name??'S',0,1)) }}</div><div class="fws" style="font-size:13px;">{{ $session->student->full_name??'—' }}</div></div></td>
<td style="font-size:13px;">{{ $session->concern_type }}</td>
<td><span class="badge {{ $session->priority==='Emergency'?'b-d':($session->priority==='Urgent'?'b-w':'b-gray') }}">{{ $session->priority }}</span></td>
<td class="mono tm" style="font-size:11px;">{{ $session->preferred_date?->format('M d Y')??'—' }}</td>
<td><span class="badge {{ $session->status==='Completed'?'b-s':($session->status==='Scheduled'?'b-p':'b-i') }}">{{ $session->status }}</span></td>
<td><a href="{{ route('counselor.counseling.index') }}" class="btn btn-o btn-sm"><i class="fas fa-eye"></i> View</a></td>
</tr>
@empty
<tr><td colspan="6" style="text-align:center;padding:28px;color:var(--tm);"><i class="fas fa-comments" style="font-size:30px;color:var(--bd);margin-bottom:10px;display:block;"></i>No counseling requests yet</td></tr>
@endforelse
</tbody></table></div>
</div>
@endsection
