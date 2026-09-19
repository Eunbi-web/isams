@extends('counselor.layouts.app')
@section('title','Announcements')
@section('page-title','Announcements')
@section('page-sub','Announcements published by the Admin Portal')
@section('content')
<div class="alert al-i an"><i class="fas fa-info-circle"></i><span>Announcements are published by the Admin team. Contact the Admin Portal to post or remove announcements.</span></div>

@forelse($announcements as $ann)
<div class="card an mb2">
<div class="ch"><i class="fas fa-bullhorn" style="color:var(--y);"></i><h2 style="font-size:15px;">{{ $ann->title }}</h2><div class="ch-acts"><span class="mono tm" style="font-size:11px;">{{ $ann->published_at?->format('M d Y')??$ann->created_at->format('M d Y') }}</span></div></div>
<div class="cb" style="font-size:13px;line-height:1.6;">{{ $ann->body }}</div>
</div>
@empty
<div class="card an">
<div style="padding:50px 20px;text-align:center;">
<i class="fas fa-bullhorn" style="font-size:40px;color:var(--bd);margin-bottom:14px;display:block;"></i>
<div style="font-size:15px;font-weight:600;color:var(--tm);">No announcements have been published yet</div>
</div>
</div>
@endforelse
@endsection
