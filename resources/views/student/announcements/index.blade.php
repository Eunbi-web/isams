@extends('student.layouts.app')
@section('title','Announcements')
@section('page-title','Announcements')
@section('page-sub','Latest scholarship news and updates')
@section('content')
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(290px,1fr));gap:16px;">
@forelse($announcements as $ann)
<div class="card an" style="transition:all .2s;" onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 10px 28px rgba(26,107,47,.14)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
<div style="background:{{ $ann->priority==='Urgent'?'linear-gradient(135deg,#7a1a14,var(--danger))':($ann->priority==='High'?'linear-gradient(135deg,#4a3800,#a07c00)':'linear-gradient(135deg,#0d3318,#1a6b2f)') }};padding:13px 17px;display:flex;align-items:center;gap:10px;">
<div style="width:36px;height:36px;border-radius:10px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-size:15px;color:var(--y);flex-shrink:0;"><i class="fas fa-bullhorn"></i></div>
<span class="badge {{ $ann->priority==='Urgent'?'b-d':($ann->priority==='High'?'b-w':'b-p') }}" style="font-size:10px;">{{ $ann->priority }}</span>
</div>
<div class="cb"><div class="fws" style="font-size:14px;margin-bottom:7px;color:var(--g);">{{ Str::limit($ann->title,50) }}</div>
<div class="tm" style="font-size:13px;line-height:1.6;margin-bottom:12px;">{{ Str::limit($ann->body,100) }}</div>
<div style="display:flex;align-items:center;justify-content:space-between;">
<div style="font-size:11px;color:var(--tm);"><i class="fas fa-clock" style="margin-right:3px;"></i>{{ $ann->created_at->diffForHumans() }}</div>
<a href="{{ route('student.announcements.show',$ann->id) }}" class="btn btn-o btn-sm" style="font-size:11px;"><i class="fas fa-arrow-right"></i> Read</a>
</div></div>
</div>
@empty
<div class="card an" style="grid-column:1/-1;"><div class="cb" style="text-align:center;padding:24px;color:var(--tm);">No announcements yet.</div></div>
@endforelse
</div>
@if($announcements->hasPages())<div style="margin-top:14px;">{{ $announcements->links() }}</div>@endif
@endsection
