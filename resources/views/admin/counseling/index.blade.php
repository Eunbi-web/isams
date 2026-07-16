@extends('admin.layouts.app')
@section('title','Counseling')
@section('page-title','Guidance Counseling')
@section('page-sub','Auto-queue management — all requests accepted')
@section('content')
<div class="alert al-i an"><i class="fas fa-info-circle"></i><span><strong>Auto-Queue Policy:</strong> All student counseling requests are automatically accepted. No requests are declined.</span></div>
<div class="sg" style="grid-template-columns:repeat(4,1fr);">
<div class="sc an"><div class="si t"><i class="fas fa-stream"></i></div><div class="sv"><div class="lbl">In Queue</div><div class="val">{{ \App\Models\CounselingSession::where('status','In Queue')->count() }}</div></div></div>
<div class="sc an"><div class="si g"><i class="fas fa-calendar-check"></i></div><div class="sv"><div class="lbl">Scheduled</div><div class="val">{{ \App\Models\CounselingSession::where('status','Scheduled')->count() }}</div></div></div>
<div class="sc an"><div class="si o"><i class="fas fa-exclamation-triangle"></i></div><div class="sv"><div class="lbl">Urgent</div><div class="val">{{ \App\Models\CounselingSession::where('priority','Urgent')->count() }}</div></div></div>
<div class="sc an"><div class="si dg"><i class="fas fa-check-circle"></i></div><div class="sv"><div class="lbl">Completed</div><div class="val">{{ \App\Models\CounselingSession::where('status','Completed')->count() }}</div></div></div>
</div>
<div class="card an">
<div class="ch" style="background:linear-gradient(135deg,var(--g),#1a5c28);"><i class="fas fa-stream" style="color:var(--y);"></i><h2 style="color:#fff;">Counseling Queue</h2><div class="ch-acts"><span style="font-size:12px;color:rgba(255,255,255,.7);">{{ $sessions->total() }} total</span></div></div>
<div class="tw"><table>
<thead><tr><th>#</th><th>Student</th><th>Concern</th><th>Priority</th><th>Preferred</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
@forelse($sessions as $ses)
<tr>
<td class="mono tm" style="font-size:11px;">#{{ $ses->queue_position??'—' }}</td>
<td><div style="display:flex;align-items:center;gap:7px;"><div class="av av-s">{{ strtoupper(substr($ses->student->first_name??'S',0,1)) }}</div><div class="fws" style="font-size:13px;">{{ $ses->student->full_name??'—' }}</div></div></td>
<td style="font-size:13px;">{{ $ses->concern_type }}</td>
<td><span class="badge {{ $ses->priority==='Urgent'?'b-d':($ses->priority==='Medium'?'b-w':'b-p') }}">{{ $ses->priority }}</span></td>
<td class="mono tm" style="font-size:11px;">{{ $ses->preferred_date?->format('M d')??'—' }} {{ $ses->preferred_time??'' }}</td>
<td><span class="badge {{ $ses->status==='Completed'?'b-s':($ses->status==='Scheduled'?'b-p':'b-i') }}">{{ $ses->status }}</span></td>
<td><div style="display:flex;gap:4px;">
@if($ses->status==='In Queue')
<button onclick="openModal('sched-{{ $ses->id }}')" class="btn btn-p btn-sm btn-ic" title="Schedule"><i class="fas fa-calendar-plus"></i></button>
@elseif($ses->status==='Scheduled')
<form method="POST" action="{{ route('admin.counseling.complete',$ses->id) }}">@csrf<button class="btn btn-s btn-sm btn-ic" title="Complete"><i class="fas fa-check"></i></button></form>
@endif
</div></td>
</tr>
@empty
<tr><td colspan="7" style="text-align:center;padding:18px;color:var(--tm);">No counseling sessions yet.</td></tr>
@endforelse
</tbody></table></div>
@if($sessions->hasPages())<div style="padding:13px 18px;border-top:1px solid var(--bd);">{{ $sessions->links() }}</div>@endif
</div>
@foreach($sessions as $ses)
@if($ses->status==='In Queue')
<div class="mo" id="sched-{{ $ses->id }}">
<div class="mb" style="max-width:480px;">
<div class="mh"><div class="si g" style="width:40px;height:40px;border-radius:11px;font-size:16px;flex-shrink:0;"><i class="fas fa-calendar-plus"></i></div><div><h3>Schedule Session</h3><div class="tm" style="font-size:12px;">{{ $ses->student->full_name??'—' }} — {{ $ses->concern_type }}</div></div><button class="mc" onclick="closeModal('sched-{{ $ses->id }}')"><i class="fas fa-times"></i></button></div>
<form method="POST" action="{{ route('admin.counseling.schedule',$ses->id) }}">@csrf
<div class="mbody">
<div class="fg"><label class="fl">Session Date</label><input type="date" name="session_date" class="fc" min="{{ date('Y-m-d') }}" required></div>
<div class="fg"><label class="fl">Session Time</label><select name="session_time" class="fc"><option>8:00 AM – 9:00 AM</option><option>9:00 AM – 10:00 AM</option><option>10:00 AM – 11:00 AM</option><option>1:00 PM – 2:00 PM</option><option>2:00 PM – 3:00 PM</option><option>3:00 PM – 4:00 PM</option></select></div>
<div class="fg"><label class="fl">Venue / Mode</label><select name="venue" class="fc"><option value="Guidance Office — Room 201">Guidance Office — Room 201</option><option value="Online (Zoom)">Online (Zoom)</option><option value="Online (Google Meet)">Online (Google Meet)</option></select></div>
<div class="fg"><label class="fl">Notes</label><textarea name="notes" class="fc" rows="2" placeholder="Instructions for student..."></textarea></div>
</div>
<div class="mfoot"><button type="button" onclick="closeModal('sched-{{ $ses->id }}')" class="btn btn-o btn-sm">Cancel</button><button type="submit" class="btn btn-p btn-sm"><i class="fas fa-calendar-check"></i> Confirm Schedule</button></div>
</form>
</div>
</div>
@endif
@endforeach
@endsection
