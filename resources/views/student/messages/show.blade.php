@extends('student.layouts.app')
@section('title','Message')
@section('page-title','Message')
@section('page-sub','Messages from the Student Affairs Office')
@section('content')
<div class="card an" style="margin-bottom:16px;display:flex;align-items:center;gap:10px;padding:14px 20px;">
<a href="{{ route('student.messages') }}" class="btn btn-o btn-sm"><i class="fas fa-arrow-left"></i> Back to Messages</a>
</div>
<div class="card an">
<div class="cb" style="padding:26px 30px;">
<div style="display:flex;align-items:flex-start;gap:14px;flex-wrap:wrap;">
<div style="width:46px;height:46px;border-radius:50%;background:var(--gp);color:var(--g);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;"><i class="fas fa-envelope"></i></div>
<div style="flex:1;">
<h2 style="font-family:'Sora',sans-serif;font-size:18px;font-weight:700;color:var(--tx);line-height:1.3;">{{ $message->subject }}</h2>
<div class="tm" style="font-size:12px;margin-top:4px;">From: <strong>Admin - Student Affairs Office</strong> · Sent {{ $message->created_at->format('F d, Y') }} at {{ $message->created_at->format('g:i A') }}</div>
</div>
@if(!$message->is_read)<span class="badge b-w">Unread</span>@endif
</div>
<hr style="border:none;border-top:1px solid var(--bd);margin:18px 0;">
<div style="background:var(--bg);border:1px solid var(--bd);border-radius:var(--rs);padding:18px 20px;font-size:14px;line-height:1.7;white-space:pre-wrap;">{{ $message->body }}</div>
</div>
</div>
@endsection
