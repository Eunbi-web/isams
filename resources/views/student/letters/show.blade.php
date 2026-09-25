@extends('student.layouts.app')
@section('title','View Letter')
@section('page-title','Confiscated Item Letter')
@section('page-sub','Letter details and current status')
@push('styles')
<style>
.lt-doc-ro{background:#ffffff;width:100%;max-width:820px;margin:0 auto;min-height:500px;padding:60px;font-family:Georgia,serif;font-size:12pt;line-height:1.8;box-shadow:0 2px 12px rgba(0,0,0,0.15);border-radius:4px;color:#202124;}
</style>
@endpush
@section('content')
<div class="card an" style="margin-bottom:16px;display:flex;align-items:center;gap:10px;padding:14px 20px;">
<a href="{{ route('student.letters') }}" class="btn btn-o btn-sm"><i class="fas fa-arrow-left"></i> Back to Letters</a>
<div style="flex:1;"></div>
<span class="badge {{ $letter->status==='Submitted'?'b-w':($letter->status==='Under Review'?'b-i':($letter->status==='Approved'?'b-s':'b-d')) }}" style="font-size:13px;padding:6px 14px;">{{ $letter->status }}</span>
</div>
<div class="card an" style="margin-bottom:16px;">
<div class="ch"><i class="fas fa-box" style="color:var(--gm);"></i><h2>Item Details</h2></div>
<div class="cb" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;">
<div><div class="lbl" style="font-size:11px;color:var(--tm);font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Item Description</div><div class="fws" style="font-size:13px;margin-top:3px;">{{ $letter->item_description }}</div></div>
<div><div class="lbl" style="font-size:11px;color:var(--tm);font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Date Confiscated</div><div class="fws" style="font-size:13px;margin-top:3px;">{{ $letter->date_confiscated ? $letter->date_confiscated->format('F d, Y') : '—' }}</div></div>
<div><div class="lbl" style="font-size:11px;color:var(--tm);font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Confiscated By</div><div class="fws" style="font-size:13px;margin-top:3px;">{{ $letter->confiscated_by ?? '—' }}</div></div>
<div><div class="lbl" style="font-size:11px;color:var(--tm);font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Reason</div><div class="fws" style="font-size:13px;margin-top:3px;">{{ $letter->reason_confiscated ?? '—' }}</div></div>
</div>
</div>
<div class="card an">
<div class="ch"><i class="fas fa-file-signature" style="color:var(--gm);"></i><h2>Letter</h2><span class="badge b-p" style="margin-left:6px;">Submitted {{ $letter->created_at->format('M d, Y') }}</span></div>
<div class="cb">
<div class="lt-doc-ro">{!! $letter->letter_content !!}</div>
</div>
</div>
@if($letter->admin_notes)
<div class="alert al-i an" style="margin-top:16px;"><i class="fas fa-comment-dots"></i><span><strong>Admin Notes:</strong> {{ $letter->admin_notes }}</span></div>
@endif
@endsection
