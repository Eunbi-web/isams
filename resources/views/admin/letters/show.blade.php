@extends('admin.layouts.app')
@section('title','Review Letter')
@section('page-title','Review Confiscated Item Letter')
@section('page-sub','Read the letter and mark it Under Review, Approved, or Rejected')
@push('styles')
<style>
.lt-doc-ro{background:#ffffff;width:100%;max-width:820px;margin:0 auto;min-height:500px;padding:60px;font-family:Georgia,serif;font-size:12pt;line-height:1.8;box-shadow:0 2px 12px rgba(0,0,0,0.15);border-radius:4px;color:#202124;}
</style>
@endpush
@section('content')
<div class="card an" style="margin-bottom:16px;display:flex;align-items:center;gap:10px;padding:14px 20px;">
<a href="{{ route('admin.letters.index') }}" class="btn btn-o btn-sm"><i class="fas fa-arrow-left"></i> Back to Letters</a>
<div style="flex:1;"></div>
<span class="badge {{ $letter->status==='Submitted'?'b-w':($letter->status==='Under Review'?'b-i':($letter->status==='Approved'?'b-s':'b-d')) }}" style="font-size:12px;">{{ $letter->status }}</span>
</div>
<div class="g2 mb3" style="align-items:start;">
<div class="card an">
<div class="ch"><i class="fas fa-box" style="color:var(--gm);"></i><h2>{{ $letter->item_description }}</h2></div>
<div class="cb">
<div style="display:flex;align-items:center;gap:12px;background:var(--bg);border:1px solid var(--bd);border-radius:var(--rs);padding:14px;margin-bottom:14px;">
<div class="av av-m">{{ strtoupper(substr($letter->student->first_name ?? 'S',0,1)) }}</div>
<div>
<div class="fwb" style="font-size:13px;">{{ $letter->student->full_name ?? 'Unknown Student' }}</div>
<div class="tm" style="font-size:12px;">EDP: <span class="mono">{{ $letter->student->student_id ?? '—' }}</span> · {{ $letter->student->course ?? '—' }} · {{ $letter->student->year_level ?? '—' }}</div>
</div>
</div>
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;">
<div><div class="lbl" style="font-size:11px;color:var(--tm);font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Date Confiscated</div><div class="fws" style="font-size:13px;margin-top:3px;">{{ $letter->date_confiscated ? $letter->date_confiscated->format('F d, Y') : '—' }}</div></div>
<div><div class="lbl" style="font-size:11px;color:var(--tm);font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Confiscated By</div><div class="fws" style="font-size:13px;margin-top:3px;">{{ $letter->confiscated_by ?? '—' }}</div></div>
<div><div class="lbl" style="font-size:11px;color:var(--tm);font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Reason</div><div class="fws" style="font-size:13px;margin-top:3px;">{{ $letter->reason_confiscated ?? '—' }}</div></div>
</div>
<div class="lbl" style="font-size:11px;color:var(--tm);font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin:16px 0 8px;">Letter Submitted {{ $letter->created_at->format('M d, Y g:i A') }}</div>
<div class="lt-doc-ro" style="max-width:100%;">{!! $letter->letter_content !!}</div>
</div>
</div>
<div class="card an">
<div class="ch"><i class="fas fa-clipboard-check" style="color:var(--gm);"></i><h2>Review Decision</h2></div>
<div class="cb">
<div style="display:flex;flex-direction:column;gap:8px;margin-bottom:16px;">
<button type="button" class="btn btn-p" id="btnApprove"><i class="fas fa-check-circle"></i> Approve Letter</button>
<button type="button" class="btn btn-o" id="btnReview"><i class="fas fa-search"></i> Mark as Under Review</button>
<button type="button" class="btn btn-d" id="btnReject"><i class="fas fa-times"></i> Reject</button>
</div>
<div class="fg"><label class="fl">Notes to Student (optional)</label>
<textarea name="admin_notes" id="adminNotes" class="fc" rows="4" placeholder="e.g. Claim your item at the SAO Office on or before Friday">{{ $letter->admin_notes }}</textarea></div>
@if($letter->reviewed_at)
<div class="alert al-s an" style="margin-top:14px;"><i class="fas fa-check-circle"></i><span>Last reviewed {{ $letter->reviewed_at->format('M d, Y g:i A') }}@if($reviewer) by {{ $reviewer->name }}@endif — status: <strong>{{ $letter->status }}</strong></span></div>
@endif
<div class="lbl" style="font-size:11px;color:var(--tm);font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin:14px 0 8px;">Timeline</div>
<div style="display:flex;flex-direction:column;gap:10px;font-size:12px;">
<div style="display:flex;align-items:center;gap:8px;"><i class="fas fa-circle" style="font-size:7px;color:var(--y);"></i><span>Submitted on <strong>{{ $letter->created_at->format('M d, Y g:i A') }}</strong></span></div>
@if($letter->reviewed_at)
<div style="display:flex;align-items:center;gap:8px;"><i class="fas fa-circle" style="font-size:7px;color:var(--gm);"></i><span>Reviewed on <strong>{{ $letter->reviewed_at->format('M d, Y g:i A') }}</strong> — <strong>{{ $letter->status }}</strong></span></div>
@endif
</div>
</div>
</div>
</div>

@push('scripts')
<script>
var CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
var STATUS_URL = '{{ route('admin.letters.status', $letter->id) }}';

function isamsToastLocal(msg, ok){
    var el = document.createElement('div');
    el.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;background:'+(ok?'#d0f0d8':'#fde8e6')+';border:1.5px solid '+(ok?'#1a7a4a':'#c0392b')+';color:'+(ok?'#0d4a1e':'#7a1a14')+';padding:12px 18px;border-radius:10px;font-size:13px;font-weight:600;max-width:360px;box-shadow:0 6px 24px rgba(0,0,0,.15);font-family:DM Sans,sans-serif;';
    el.textContent = msg;
    document.body.appendChild(el);
    setTimeout(function(){ el.remove(); }, 3500);
}

function setStatus(status){
    var btns = [document.getElementById('btnApprove'), document.getElementById('btnReview'), document.getElementById('btnReject')];
    btns.forEach(function(b){ b && (b.disabled = true); });
    fetch(STATUS_URL, {method:'PATCH', headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest','Accept':'application/json'}, body: JSON.stringify({status:status, admin_notes: document.getElementById('adminNotes').value})})
    .then(function(r){ return r.json(); })
    .then(function(d){ isamsToastLocal(d.message || 'Status updated.', true); setTimeout(function(){ location.reload(); }, 800); })
    .catch(function(){ isamsToastLocal('Failed to update status.', false); btns.forEach(function(b){ b && (b.disabled = false); }); });
}

document.getElementById('btnApprove') && document.getElementById('btnApprove').addEventListener('click', function(){ setStatus('Approved'); });
document.getElementById('btnReview')  && document.getElementById('btnReview').addEventListener('click', function(){ setStatus('Under Review'); });
document.getElementById('btnReject')  && document.getElementById('btnReject').addEventListener('click', function(){ setStatus('Rejected'); });
</script>
@endpush
@endsection
