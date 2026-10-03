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
<button type="button" onclick="openNarrative({{ $ses->id }})" class="btn btn-o btn-sm btn-ic" style="background:var(--gp);color:var(--gm);" title="AI Narrative Report"><i class="fas fa-robot"></i></button>
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

{{-- AI Narrative Report Modal (Groq) --}}
<div class="mo" id="narrativeModal">
<div class="mb" style="max-width:760px;">
<div class="mh"><div class="si g" style="width:40px;height:40px;border-radius:11px;font-size:16px;flex-shrink:0;"><i class="fas fa-robot"></i></div><div><h3>AI Narrative Report</h3><div class="tm" style="font-size:12px;" id="narrativeSub">—</div></div><button class="mc" onclick="closeModal('narrativeModal')"><i class="fas fa-times"></i></button></div>
<div class="mbody">
<div id="narrativeLoading" style="text-align:center;padding:26px 10px;color:var(--tm);font-size:13px;">
<div style="display:inline-flex;align-items:center;gap:9px;"><div style="width:15px;height:15px;border:2px solid var(--gm);border-top-color:transparent;border-radius:50%;animation:narSpin .7s linear infinite;"></div> Generating detailed report with Groq AI...</div>
</div>
<div id="narrativeError" style="display:none;" class="alert al-d"><i class="fas fa-exclamation-circle"></i> <span id="narrativeErrorText"></span></div>
<div id="narrativeReport" class="nr-body" style="display:none;background:#fff;border:1px solid var(--bd);border-radius:var(--rs);padding:24px 26px;max-height:460px;overflow-y:auto;font-size:13px;line-height:1.7;"></div>
</div>
<div class="mfoot"><button type="button" onclick="closeModal('narrativeModal')" class="btn btn-o btn-sm">Close</button><button type="button" id="narrativePrintBtn" style="display:none;" onclick="printNarrative()" class="btn btn-p btn-sm"><i class="fas fa-print"></i> Print Report</button></div>
</div>
</div>
<style>
@keyframes narSpin{to{transform:rotate(360deg);}}
.nr-body h2{text-align:center;margin:4px 0 2px;font-size:17px;}
.nr-body h3{border-bottom:2px solid var(--gm);color:var(--g);padding-bottom:4px;margin-top:22px;font-size:13.5px;}
.nr-body table{width:100%;border-collapse:collapse;margin:10px 0 14px;font-size:12px;}
.nr-body th,.nr-body td{border:1px solid var(--bd);padding:6px 10px;text-align:left;vertical-align:top;}
.nr-body th{background:var(--gp);width:32%;}
.nr-body ul,.nr-body ol{margin:8px 0 14px 24px;}
.nr-body li{margin-bottom:5px;}
.nr-body p{margin:8px 0;text-align:justify;}
</style>
@endsection
@push('scripts')
<script>
// AI Narrative Report (Groq) — DSA portal
var narrativeId = null, narrativeText = '';
function openNarrative(id){
    narrativeId = id;
    openModal('narrativeModal');
    document.getElementById('narrativeLoading').style.display='';
    document.getElementById('narrativeError').style.display='none';
    document.getElementById('narrativeReport').style.display='none';
    document.getElementById('narrativePrintBtn').style.display='none';
    document.getElementById('narrativeSub').textContent='Generating...';
    var csrf=document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch('{{ route('admin.counseling.narrative', '__ID__') }}'.replace('__ID__', id), {
        method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}
    }).then(function(r){ return r.json().catch(function(){ return {}; }); })
      .then(function(d){
          document.getElementById('narrativeLoading').style.display='none';
          if(d.report){
              narrativeText = d.report;
              document.getElementById('narrativeReport').innerHTML = d.report;
              document.getElementById('narrativeReport').style.display='';
              document.getElementById('narrativePrintBtn').style.display='';
              document.getElementById('narrativeSub').textContent='Generated with Groq AI — review before filing.';
              window.isamsToast ? isamsToast(d.message || 'Narrative report generated.', 'success') : null;
          } else {
              document.getElementById('narrativeErrorText').textContent = d.message || 'Could not generate the report. Please try again.';
              document.getElementById('narrativeError').style.display='';
          }
      }).catch(function(){
          document.getElementById('narrativeLoading').style.display='none';
          document.getElementById('narrativeErrorText').textContent='Network error while contacting the AI service.';
          document.getElementById('narrativeError').style.display='';
      });
}
function narrativePrintCss(){
    return '<style>body{font-family:Georgia,serif;font-size:13px;line-height:1.8;color:#111;max-width:720px;margin:40px auto;padding:0 24px;}'
        +'h2{text-align:center;margin:6px 0 2px;font-size:18px;}h3{border-bottom:2px solid #1a6b2f;color:#1a6b2f;padding-bottom:4px;margin-top:26px;font-size:14px;}'
        +'table{width:100%;border-collapse:collapse;margin:10px 0 16px;font-size:12.5px;}'
        +'th,td{border:1px solid #999;padding:6px 10px;text-align:left;vertical-align:top;}th{background:#e8f5ec;width:32%;}td{width:68%;}'
        +'ul,ol{margin:8px 0 16px 26px;}li{margin-bottom:6px;}p{margin:8px 0;text-align:justify;}.sig{margin-top:40px;}</style>';
}
function printNarrative(){
    var w = window.open('', '_blank', 'width=860,height=950');
    w.document.write('<html><head><title>Narrative Report</title>'+narrativePrintCss()+'</head><body>');
    w.document.write(document.getElementById('narrativeReport').innerHTML);
    w.document.write('</body></html>');
    w.document.close();
    w.focus();
    setTimeout(function(){ w.print(); }, 400);
}
</script>
@endpush
