@extends('student.layouts.app')
@section('title','Counseling')
@section('page-title','Guidance Counseling')
@section('page-sub','Request a session — auto-queued, all requests accepted')
@section('content')
<div class="alert al-i an"><i class="fas fa-info-circle"></i><span><strong>Auto-Queue:</strong> All counseling requests are automatically accepted and queued. No requests are declined.</span></div>
<div class="g2" style="align-items:start;">
<div class="card an"><div class="ch"><i class="fas fa-plus-circle" style="color:var(--gm);"></i><h2>Request Counseling Session</h2></div><div class="cb">
<form method="POST" action="{{ route('student.counseling.store') }}">@csrf
<div class="fg"><label class="fl">Type of Concern <span style="color:var(--danger);">*</span></label><select name="concern_type" class="fc" required><option value="">Select concern type...</option>@foreach(['Academic Stress','Personal Issue','Career Guidance','Mental Health / Anxiety','Family Concern','Financial Stress','Relationship Issue','General Wellness'] as $type)<option value="{{ $type }}">{{ $type }}</option>@endforeach</select></div>
<div class="fg"><label class="fl">Priority Level</label><div style="display:grid;grid-template-columns:repeat(3,1fr);gap:7px;">@foreach([['Normal','fas fa-circle'],['Medium','fas fa-exclamation'],['Urgent','fas fa-exclamation-triangle']] as $p)<label style="display:flex;align-items:center;gap:6px;padding:8px 10px;border:1.5px solid var(--bd);border-radius:var(--rs);cursor:pointer;font-size:13px;"><input type="radio" name="priority" value="{{ $p[0] }}" {{ $p[0]==='Normal'?'checked':'' }} style="accent-color:var(--g);"><i class="{{ $p[1] }}" style="font-size:11px;"></i> {{ $p[0] }}</label>@endforeach</div></div>
<div class="fg"><label class="fl">Preferred Date <span style="font-size:11px;color:var(--tm);font-weight:400;">(optional)</span></label><input type="date" name="preferred_date" class="fc" min="{{ date('Y-m-d',strtotime('+1 day')) }}"></div>
<div class="fg"><label class="fl">Preferred Time</label><select name="preferred_time" class="fc"><option value="">No preference</option><option>8:00 AM – 9:00 AM</option><option>9:00 AM – 10:00 AM</option><option>10:00 AM – 11:00 AM</option><option>1:00 PM – 2:00 PM</option><option>2:00 PM – 3:00 PM</option><option>3:00 PM – 4:00 PM</option></select></div>
<div class="fg"><label class="fl">Brief Description</label><textarea name="concern_detail" class="fc" rows="4" placeholder="Briefly describe what you'd like to discuss..."></textarea></div>
<div style="background:var(--gp);border-radius:var(--rs);padding:11px 13px;margin-bottom:14px;font-size:12px;color:var(--g);"><i class="fas fa-shield-alt" style="margin-right:6px;color:var(--gm);"></i><strong>Confidential:</strong> Your session details are strictly confidential.</div>
<button type="submit" class="btn btn-p" style="width:100%;justify-content:center;"><i class="fas fa-paper-plane"></i> Submit Request — Auto Queue</button>
</form></div></div>
<div>
<div class="card an mb3"><div class="ch"><i class="fas fa-list" style="color:var(--gm);"></i><h2>My Sessions</h2></div>
<div class="cb" style="padding:0;">
@forelse($sessions as $ses)
<div style="display:flex;align-items:center;gap:11px;padding:13px 18px;border-bottom:1px solid var(--bd);">
<div style="width:40px;height:40px;border-radius:11px;background:{{ $ses->status==='Completed'?'#d0f0d8':($ses->status==='Scheduled'?'var(--gp)':'#e0f3f8') }};display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-{{ $ses->status==='Completed'?'check-circle':($ses->status==='Scheduled'?'calendar-check':'clock') }}" style="color:{{ $ses->status==='Completed'?'#0d6624':($ses->status==='Scheduled'?'var(--gm)':'var(--info)') }};font-size:15px;"></i></div>
<div style="flex:1;"><div class="fws" style="font-size:13px;">{{ $ses->concern_type }}</div><div class="tm" style="font-size:12px;">{{ $ses->session_date?->format('M d, Y')??' — '  }} {{ $ses->session_time??'' }}</div></div>
<span class="badge {{ $ses->status==='Completed'?'b-s':($ses->status==='Scheduled'?'b-p':'b-i') }}">{{ $ses->status }}</span>
</div>
@empty
<div style="padding:20px;text-align:center;color:var(--tm);font-size:13px;">No sessions yet. Submit a request above.</div>
@endforelse
</div></div>
<div class="card an"><div class="ch" style="background:linear-gradient(135deg,var(--g),#1a5c28);"><i class="fas fa-stream" style="color:var(--y);"></i><h2 style="color:#fff;">Queue Status</h2></div>
<div class="cb" style="text-align:center;">
<div style="width:70px;height:70px;border-radius:50%;background:linear-gradient(135deg,var(--info),#1a5580);display:inline-flex;align-items:center;justify-content:center;margin-bottom:10px;"><div style="font-family:'JetBrains Mono',monospace;font-size:22px;font-weight:800;color:#fff;">#{{ $queuePos + 1 }}</div></div>
<div class="fws" style="font-size:14px;color:var(--g);">Queue Size</div>
<div class="tm" style="font-size:12px;margin-top:3px;">{{ $queuePos }} ahead in queue</div>
<div class="alert al-i mt3" style="margin-bottom:0;font-size:12px;"><i class="fas fa-bell"></i><span>You'll be notified when your session is scheduled.</span></div>
</div></div>
</div>
</div>
@endsection
