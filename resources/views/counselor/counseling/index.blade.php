@extends('counselor.layouts.app')
@section('title','Counseling Sessions')
@section('page-title','Counseling Sessions')
@section('page-sub','Manage and schedule guidance counseling requests')
@section('content')
<div class="card mb3">
<div class="cb" style="padding:14px 18px;">
<form method="GET" action="{{ route('counselor.counseling.index') }}" id="cFilterForm" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
<input type="text" name="search" id="cSearch" class="fc" placeholder="Search by student name or concern type" value="{{ request('search') }}" style="flex:1;min-width:200px;">
<select name="status" class="fc" style="width:160px;" onchange="document.getElementById('cFilterForm').submit()">
<option value="">All Status</option>
<option value="In Queue" {{ request('status')==='In Queue'?'selected':'' }}>In Queue</option>
<option value="Scheduled" {{ request('status')==='Scheduled'?'selected':'' }}>Scheduled</option>
<option value="Completed" {{ request('status')==='Completed'?'selected':'' }}>Completed</option>
</select>
<select name="priority" class="fc" style="width:160px;" onchange="document.getElementById('cFilterForm').submit()">
<option value="">All Priority</option>
<option value="Normal" {{ request('priority')==='Normal'?'selected':'' }}>Normal</option>
<option value="Urgent" {{ request('priority')==='Urgent'?'selected':'' }}>Urgent</option>
<option value="Emergency" {{ request('priority')==='Emergency'?'selected':'' }}>Emergency</option>
</select>
<a href="{{ route('counselor.counseling.index') }}" class="btn btn-o btn-sm"><i class="fas fa-rotate-left"></i> Clear Filters</a>
</form>
</div>
</div>

<div class="card an" id="cCounselingCard">
<div class="ch"><i class="fas fa-comments" style="color:var(--gm);"></i><h2>Counseling Sessions</h2><span class="badge b-p">{{ $sessions->total() }}</span><div class="ch-acts"><button type="button" onclick="window.print()" class="btn btn-o btn-sm"><i class="fas fa-print"></i> Print</button></div></div>
<div class="tw"><table>
<thead><tr><th>No.</th><th>Student Name and Course</th><th>Concern Type</th><th>Concern Detail</th><th>Priority</th><th>Preferred Date</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
@forelse($sessions as $i=>$session)
<tr @if($session->status==='In Queue')style="background:rgba(26,107,47,0.04);"@elseif($session->status==='Completed')style="background:rgba(45,158,79,0.07);"@endif>
<td class="mono tm" style="font-size:11px;">{{ ($sessions->currentPage()-1)*$sessions->perPage()+$i+1 }}</td>
<td><div style="display:flex;align-items:center;gap:7px;"><div class="av av-s">{{ strtoupper(substr($session->student->first_name??'S',0,1)) }}</div><div><div class="fws" style="font-size:13px;">{{ $session->student->full_name??'—' }}</div><div class="tm" style="font-size:11px;">{{ $session->student->course??'—' }}</div></div></div></td>
<td style="font-size:13px;">{{ $session->concern_type }}</td>
<td style="font-size:12px;color:var(--tm);max-width:220px;">{{ \Illuminate\Support\Str::limit($session->concern_detail??'—',55) }}</td>
<td><span class="badge {{ $session->priority==='Emergency'?'b-d':($session->priority==='Urgent'?'b-w':'b-gray') }}">{{ $session->priority }}</span></td>
<td class="mono tm" style="font-size:11px;">{{ $session->preferred_date?->format('M d Y')??'—' }}</td>
<td><span class="badge {{ $session->status==='Completed'?'b-s':($session->status==='Scheduled'?'b-p':'b-i') }}">{{ $session->status }}</span></td>
<td><div style="display:flex;gap:4px;">
@if($session->status!=='Completed')
<button type="button" onclick="openModal('scheduleModal-{{ $session->id }}')" class="btn btn-sm" style="background:#1a4a6b;color:#fff;" title="Schedule"><i class="fas fa-calendar-plus"></i></button>
<button type="button" onclick="openModal('completeModal-{{ $session->id }}')" class="btn btn-s btn-sm" title="Complete"><i class="fas fa-check"></i></button>
@endif
<button type="button" class="btn btn-o btn-sm" style="background:var(--gp);color:var(--gm);" onclick="openNarrative({{ $session->id }})" title="AI Narrative Report"><i class="fas fa-robot"></i></button>
<button type="button" class="btn btn-d btn-sm" data-ajax-delete="true" data-url="{{ route('counselor.counseling.destroy', $session->id) }}" data-confirm="Remove this counseling request?" title="Delete"><i class="fas fa-trash"></i></button>
</div></td>
</tr>
@empty
<tr><td colspan="8" style="text-align:center;padding:28px;color:var(--tm);"><i class="fas fa-comments" style="font-size:30px;color:var(--bd);margin-bottom:10px;display:block;"></i>No counseling sessions found</td></tr>
@endforelse
</tbody></table></div>
@if($sessions->hasPages())<div class="c-pagination" style="padding:13px 18px;border-top:1px solid var(--bd);">{{ $sessions->links() }}</div>@endif
</div>

@foreach($sessions as $session)
@if($session->status!=='Completed')
{{-- Schedule Modal --}}
<div class="mo" id="scheduleModal-{{ $session->id }}">
<div class="mb" style="max-width:480px;">
<div class="mh"><div class="si g" style="width:40px;height:40px;border-radius:11px;font-size:16px;flex-shrink:0;"><i class="fas fa-calendar-plus"></i></div><div><h3>Schedule Session</h3><div class="tm" style="font-size:12px;">{{ $session->student->full_name??'—' }} — {{ $session->concern_type }}</div></div><button class="mc" onclick="closeModal('scheduleModal-{{ $session->id }}')"><i class="fas fa-times"></i></button></div>
<form method="POST" action="{{ route('counselor.counseling.schedule', $session->id) }}" data-ajax="true">@csrf
<div class="mbody">
<div class="fg"><label class="fl">Session Date <span style="color:var(--danger)">*</span></label><input type="date" name="session_date" class="fc" min="{{ date('Y-m-d') }}" required></div>
<div class="fg"><label class="fl">Session Time</label><input type="text" name="session_time" class="fc" placeholder="e.g. 9:00 AM to 10:00 AM"></div>
<div class="fg"><label class="fl">Venue</label><input type="text" name="venue" class="fc" placeholder="e.g. Guidance Office Room 101"></div>
<div class="fg"><label class="fl">Priority</label><select name="priority" class="fc">
@foreach(['Normal','Urgent','Emergency'] as $p)
<option value="{{ $p }}" {{ $session->priority===$p?'selected':'' }}>{{ $p }}</option>
@endforeach
</select></div>
<div class="fg"><label class="fl">Notes for Student</label><textarea name="notes" class="fc" rows="3" placeholder="Instructions for student..."></textarea></div>
</div>
<div class="mfoot"><button type="button" onclick="closeModal('scheduleModal-{{ $session->id }}')" class="btn btn-o btn-sm">Cancel</button><button type="submit" class="btn btn-p btn-sm"><i class="fas fa-calendar-check"></i> Schedule Session</button></div>
</form>
</div>
</div>

{{-- Complete Modal --}}
<div class="mo" id="completeModal-{{ $session->id }}">
<div class="mb" style="max-width:480px;">
<div class="mh"><div class="si dg" style="width:40px;height:40px;border-radius:11px;font-size:16px;flex-shrink:0;"><i class="fas fa-check-circle"></i></div><div><h3>Complete Session</h3><div class="tm" style="font-size:12px;">{{ $session->student->full_name??'—' }} — {{ $session->concern_type }}</div></div><button class="mc" onclick="closeModal('completeModal-{{ $session->id }}')"><i class="fas fa-times"></i></button></div>
<form method="POST" action="{{ route('counselor.counseling.complete', $session->id) }}" data-ajax="true">@csrf
<div class="mbody">
<div class="fg"><label class="fl">Session Notes <span style="color:var(--danger)">*</span></label><textarea name="notes" class="fc" rows="4" placeholder="Write session summary and outcomes" required></textarea></div>
<div class="fg" style="display:flex;align-items:center;gap:8px;"><input type="checkbox" name="follow_up_required" id="followup-{{ $session->id }}" value="1" class="followup-check" data-target="followupDate-{{ $session->id }}" style="width:auto;accent-color:var(--g);"><label for="followup-{{ $session->id }}" class="fl" style="margin:0;">Follow-up Required</label></div>
<div class="fg" id="followupDate-{{ $session->id }}" style="display:none;"><label class="fl">Follow-up Date</label><input type="date" name="follow_up_date" class="fc" min="{{ date('Y-m-d') }}"></div>
</div>
<div class="mfoot"><button type="button" onclick="closeModal('completeModal-{{ $session->id }}')" class="btn btn-o btn-sm">Cancel</button><button type="submit" class="btn btn-s btn-sm"><i class="fas fa-check"></i> Mark as Completed</button></div>
</form>
</div>
</div>
@endif
@endforeach

{{-- AI Narrative Report Modal --}}
<div class="mo" id="narrativeModal">
<div class="mb" style="max-width:720px;">
<div class="mh"><div class="si g" style="width:40px;height:40px;border-radius:11px;font-size:16px;flex-shrink:0;"><i class="fas fa-robot"></i></div><div><h3>AI Narrative Report</h3><div class="tm" style="font-size:12px;" id="narrativeSub">—</div></div><button class="mc" onclick="closeModal('narrativeModal')"><i class="fas fa-times"></i></button></div>
<div class="mbody">
<div id="narrativeLoading" style="text-align:center;padding:26px 10px;color:var(--tm);font-size:13px;">
<div style="display:inline-flex;align-items:center;gap:9px;"><div style="width:15px;height:15px;border:2px solid var(--gm);border-top-color:transparent;border-radius:50%;animation:narSpin .7s linear infinite;"></div> Generating report with Groq AI...</div>
</div>
<div id="narrativeError" style="display:none;" class="alert al-d"><i class="fas fa-exclamation-circle"></i> <span id="narrativeErrorText"></span></div>
<div id="narrativeReport" style="display:none;background:var(--bg);border:1px solid var(--bd);border-radius:var(--rs);padding:20px 22px;max-height:420px;overflow-y:auto;font-size:13.5px;line-height:1.75;white-space:pre-wrap;"></div>
</div>
<div class="mfoot"><button type="button" onclick="closeModal('narrativeModal')" class="btn btn-o btn-sm">Close</button><button type="button" id="narrativePrintBtn" style="display:none;" onclick="printNarrative()" class="btn btn-p btn-sm"><i class="fas fa-print"></i> Print Report</button></div>
</div>
</div>
<style>
@keyframes narSpin{to{transform:rotate(360deg);}}
</style>

<style>
@media print{
.sidebar,.topbar,.ai-bar,.card:first-of-type,.c-pagination,.btn,.mob-toggle{display:none !important;}
body{background:var(--bg);}
.main{margin-left:0;}
.card{border:none;box-shadow:none;}
.card .ch{display:none;}
}
</style>
@endsection
@push('scripts')
<script>
// Live search with 400ms debounce
(function(){
    var t;
    var inp=document.getElementById('cSearch');
    if(inp) inp.addEventListener('keyup',function(){
        clearTimeout(t);
        t=setTimeout(function(){ document.getElementById('cFilterForm').submit(); },400);
    });
})();
// Follow-up checkbox toggle
document.addEventListener('change',function(e){
    var cb=e.target;
    if(cb.classList&&cb.classList.contains('followup-check')){
        var target=document.getElementById(cb.dataset.target);
        if(target) target.style.display=cb.checked?'':'none';
    }
});
// AI Narrative Report (Groq)
var narrativeId = null, narrativeText = '', narrativeMeta = '';
function openNarrative(id){
    narrativeId = id;
    openModal('narrativeModal');
    document.getElementById('narrativeLoading').style.display='';
    document.getElementById('narrativeError').style.display='none';
    document.getElementById('narrativeReport').style.display='none';
    document.getElementById('narrativePrintBtn').style.display='none';
    document.getElementById('narrativeSub').textContent='Generating...';
    var csrf=document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch('{{ route('counselor.counseling.narrative', '__ID__') }}'.replace('__ID__', id), {
        method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}
    }).then(function(r){ return r.json().catch(function(){ return {}; }); })
      .then(function(d){
          document.getElementById('narrativeLoading').style.display='none';
          if(d.report){
              narrativeText = d.report;
              document.getElementById('narrativeReport').textContent = d.report;
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
function printNarrative(){
    var w = window.open('', '_blank', 'width=820,height=900');
    w.document.write('<html><head><title>Narrative Report</title><style>body{font-family:Georgia,serif;font-size:13px;line-height:1.8;color:#111;max-width:700px;margin:40px auto;padding:0 20px;white-space:pre-wrap;}h1{text-align:center;font-size:16px;}</style></head><body>');
    w.document.write('<div style="text-align:center;font-weight:bold;">SAINT COLUMBAN COLLEGE</div><div style="text-align:center;font-size:11px;margin-bottom:18px;">Guidance Counseling Office — Narrative Report</div>');
    w.document.write(document.getElementById('narrativeReport').textContent.replace(/&/g,'&amp;').replace(/</g,'&lt;'));
    w.document.write('</body></html>');
    w.document.close();
    w.focus();
    setTimeout(function(){ w.print(); }, 400);
}
</script>
@endpush
