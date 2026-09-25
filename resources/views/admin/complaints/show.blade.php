@extends('admin.layouts.app')
@section('title','Complaint Details')
@section('page-title','Complaint Details')
@section('page-sub','Review and respond to a student complaint or report')
@section('content')
<div class="card an" style="margin-bottom:16px;display:flex;align-items:center;gap:10px;padding:14px 20px;">
<a href="{{ route('admin.complaints.index') }}" class="btn btn-o btn-sm"><i class="fas fa-arrow-left"></i> Back to Inbox</a>
<div style="flex:1;"></div>
<span class="badge {{ $complaint->status==='Pending'?'b-w':($complaint->status==='Under Review'?'b-i':($complaint->status==='Resolved'?'b-s':'b-gray')) }}" style="font-size:12px;">{{ $complaint->status }}</span>
</div>
<div class="g2 mb3" style="align-items:start;">
<div class="card an">
<div class="ch"><i class="fas fa-exclamation-circle" style="color:var(--danger);"></i><h2>{{ $complaint->subject }}</h2></div>
<div class="cb">
<div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px;">
<span class="badge {{ $complaint->type==='Complaint'?'b-d':'b-i' }}">{{ $complaint->type }}</span>
@if($complaint->is_anonymous)<span class="badge b-d"><i class="fas fa-user-secret"></i> Anonymous</span>@endif
<span class="badge b-p">Submitted {{ $complaint->created_at->format('M d, Y g:i A') }}</span>
</div>
@if($complaint->is_anonymous)
<div style="display:flex;align-items:center;gap:12px;background:var(--bg);border:1px solid var(--bd);border-radius:var(--rs);padding:14px;margin-bottom:14px;">
<div style="width:42px;height:42px;border-radius:50%;background:#fde8e6;color:var(--danger);display:flex;align-items:center;justify-content:center;font-size:16px;"><i class="fas fa-user-secret"></i></div>
<div><div class="fwb" style="font-size:13px;">Anonymous Student</div><div class="tm" style="font-size:12px;font-style:italic;">Identity hidden at the student's request</div></div>
</div>
@else
<div style="display:flex;align-items:center;gap:12px;background:var(--bg);border:1px solid var(--bd);border-radius:var(--rs);padding:14px;margin-bottom:14px;">
<div class="av av-m">{{ strtoupper(substr($complaint->student->first_name ?? 'S',0,1)) }}</div>
<div>
<div class="fwb" style="font-size:13px;">{{ $complaint->student->full_name ?? 'Unknown Student' }}</div>
<div class="tm" style="font-size:12px;">EDP: <span class="mono">{{ $complaint->student->student_id ?? '—' }}</span> · {{ $complaint->student->course ?? '—' }} · {{ $complaint->student->year_level ?? '—' }}</div>
</div>
</div>
@endif
<div class="lbl" style="font-size:11px;color:var(--tm);font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Complaint Description</div>
<div style="background:var(--bg);border:1px solid var(--bd);border-radius:var(--rs);padding:16px;font-size:14px;line-height:1.6;white-space:pre-wrap;">{{ $complaint->description }}</div>
</div>
</div>
<div class="card an">
<div class="ch"><i class="fas fa-reply" style="color:var(--gm);"></i><h2>Admin Reply</h2></div>
<div class="cb">
@if($complaint->admin_reply)
<div style="background:#d0f0d8;border:1px solid #a0d8b0;border-left:4px solid #1a7a4a;border-radius:var(--rs);padding:16px;margin-bottom:14px;">
<div class="fwb" style="font-size:13px;color:#0d4a1e;margin-bottom:6px;"><i class="fas fa-check-circle"></i> Reply Sent — Resolved</div>
<div style="font-size:13px;line-height:1.6;color:#0d4a1e;white-space:pre-wrap;">{{ $complaint->admin_reply }}</div>
<div class="tm" style="font-size:11px;margin-top:8px;">Replied {{ $complaint->replied_at?->format('M d, Y g:i A') }}</div>
</div>
@else
<form id="replyForm">
<div class="fg"><label class="fl">Reply to Student</label>
<textarea name="reply" id="replyText" class="fc" rows="5" required placeholder="Write your response to the student"></textarea></div>
<div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;">
<button type="submit" class="btn btn-p" id="replyBtn"><i class="fas fa-reply"></i> Reply and Resolve</button>
<button type="button" class="btn btn-o btn-sm" onclick="setStatus('Under Review')"><i class="fas fa-search"></i> Mark as Under Review</button>
<button type="button" class="btn btn-d btn-sm" onclick="setStatus('Dismissed')"><i class="fas fa-times"></i> Dismiss</button>
</div>
</form>
@endif
<div class="lbl" style="font-size:11px;color:var(--tm);font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">Status History</div>
<div style="display:flex;flex-direction:column;gap:10px;font-size:12px;">
<div style="display:flex;align-items:center;gap:8px;"><i class="fas fa-circle" style="font-size:7px;color:var(--gm);"></i><span>Filed on <strong>{{ $complaint->created_at->format('M d, Y g:i A') }}</strong> as <strong>{{ $complaint->type }}</strong></span></div>
<div style="display:flex;align-items:center;gap:8px;"><i class="fas fa-circle" style="font-size:7px;color:var(--y);"></i><span>Current status: <strong>{{ $complaint->status }}</strong></span></div>
@if($complaint->replied_at)
<div style="display:flex;align-items:center;gap:8px;"><i class="fas fa-circle" style="font-size:7px;color:var(--gm);"></i><span>Replied on <strong>{{ $complaint->replied_at->format('M d, Y g:i A') }}</strong> — marked Resolved</span></div>
@endif
</div>
</div>
</div>
</div>

@push('scripts')
<script>
var CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
var REPLY_URL = '{{ route('admin.complaints.reply', $complaint->id) }}';
var STATUS_URL = '{{ route('admin.complaints.status', $complaint->id) }}';

function isamsToastLocal(msg, ok){
    var el = document.createElement('div');
    el.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;background:'+(ok?'#d0f0d8':'#fde8e6')+';border:1.5px solid '+(ok?'#1a7a4a':'#c0392b')+';color:'+(ok?'#0d4a1e':'#7a1a14')+';padding:12px 18px;border-radius:10px;font-size:13px;font-weight:600;max-width:360px;box-shadow:0 6px 24px rgba(0,0,0,.15);font-family:DM Sans,sans-serif;';
    el.textContent = msg;
    document.body.appendChild(el);
    setTimeout(function(){ el.remove(); }, 3500);
}

function setStatus(status){
    fetch(STATUS_URL, {method:'PATCH', headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest','Accept':'application/json'}, body: JSON.stringify({status:status})})
    .then(function(r){ return r.json(); })
    .then(function(d){ isamsToastLocal(d.message || 'Status updated.', true); setTimeout(function(){ location.reload(); }, 800); })
    .catch(function(){ isamsToastLocal('Failed to update status.', false); });
}

document.getElementById('replyForm') && document.getElementById('replyForm').addEventListener('submit', function(e){
    e.preventDefault();
    var btn = document.getElementById('replyBtn');
    btn.disabled = true;
    fetch(REPLY_URL, {method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest','Accept':'application/json'}, body: JSON.stringify({reply: document.getElementById('replyText').value})})
    .then(function(r){ return r.json(); })
    .then(function(d){ isamsToastLocal(d.message || 'Reply sent.', true); setTimeout(function(){ location.reload(); }, 800); })
    .catch(function(){ isamsToastLocal('Failed to send reply.', false); btn.disabled = false; });
});
</script>
@endpush
@endsection
