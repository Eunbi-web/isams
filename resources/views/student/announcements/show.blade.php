@extends('student.layouts.app')
@section('title','Announcement')
@section('page-title','Announcement')
@section('page-sub','{{ $announcement->title }}')
@section('content')
<div style="max-width:720px;">
<div style="margin-bottom:14px;"><a href="{{ route('student.announcements') }}" class="btn btn-o btn-sm"><i class="fas fa-arrow-left"></i> Back</a></div>
<div class="card an"><div class="ch"><i class="fas fa-bullhorn" style="color:var(--gm);"></i><h2>{{ $announcement->title }}</h2><span class="badge {{ $announcement->priority==='Urgent'?'b-d':($announcement->priority==='High'?'b-w':'b-gray') }}" style="margin-left:6px;">{{ $announcement->priority }}</span></div>
<div class="cb"><div style="font-size:14px;line-height:1.8;color:var(--tx);">{{ $announcement->body }}</div>
<div style="margin-top:18px;padding-top:14px;border-top:1px solid var(--bd);font-size:12px;color:var(--tm);"><i class="fas fa-clock" style="margin-right:4px;"></i>Posted {{ $announcement->created_at->diffForHumans() }}</div>
</div></div>
</div>
@endsection
