@extends('admin.layouts.app')
@section('title','Complaints and Reports')
@section('page-title','Complaints and Reports')
@section('page-sub','Inbox of complaints and reports filed by students')
@push('styles')
<style>
.inbox-row{display:flex;align-items:center;padding:14px 18px;border-bottom:1px solid var(--bd);cursor:pointer;transition:background .15s;border-left:3px solid transparent;}
.inbox-row:hover{background:var(--bg);}
.inbox-row.st-pending{border-left:3px solid var(--y);}
.inbox-row.st-resolved{border-left:3px solid var(--gm);}
.inbox-avatar{width:38px;height:38px;border-radius:50%;background:var(--gp);color:var(--g);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:14px;flex-shrink:0;}
.inbox-mid{flex:1;min-width:0;margin-left:13px;}
.inbox-subject{font-weight:700;font-size:14px;color:var(--tx);}
.inbox-meta{font-size:12px;color:var(--tm);margin-top:2px;display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
.inbox-right{margin-left:auto;display:flex;align-items:center;gap:10px;flex-shrink:0;}
.finput{padding:8px 12px;border:1.5px solid var(--bd);border-radius:var(--rs);font-size:13px;font-family:inherit;background:var(--card);color:var(--tx);}
</style>
@endpush
@section('content')
<div class="sg">
<div class="sc an d1"><div class="si t"><i class="fas fa-inbox"></i></div><div class="sv"><div class="lbl">Total Complaints</div><div class="val">{{ $stats['total'] }}</div></div></div>
<div class="sc an d2"><div class="si y"><i class="fas fa-clock"></i></div><div class="sv"><div class="lbl">Pending</div><div class="val">{{ $stats['pending'] }}</div></div></div>
<div class="sc an d3"><div class="si o"><i class="fas fa-search"></i></div><div class="sv"><div class="lbl">Under Review</div><div class="val">{{ $stats['under_review'] }}</div></div></div>
<div class="sc an d4"><div class="si g"><i class="fas fa-check-circle"></i></div><div class="sv"><div class="lbl">Resolved</div><div class="val">{{ $stats['resolved'] }}</div></div></div>
</div>

<div class="card mb3 an" style="padding:14px 18px;">
<div style="display:flex;gap:9px;flex-wrap:wrap;align-items:center;">
<input type="text" id="inboxSearch" class="finput" style="flex:1;min-width:220px;" placeholder="Search by subject or student name" value="{{ $filters['search'] }}">
<select id="inboxType" class="finput" style="width:160px;">
<option value="">All Types</option>
<option value="Complaint" {{ $filters['type']==='Complaint'?'selected':'' }}>Complaint</option>
<option value="Report" {{ $filters['type']==='Report'?'selected':'' }}>Report</option>
</select>
<select id="inboxStatus" class="finput" style="width:170px;">
<option value="">All Status</option>
@foreach(['Pending','Under Review','Resolved','Dismissed'] as $st)
<option value="{{ $st }}" {{ $filters['status']===$st?'selected':'' }}>{{ $st }}</option>
@endforeach
</select>
<a href="{{ route('admin.complaints.index') }}" class="btn btn-o btn-sm"><i class="fas fa-times"></i> Clear Filters</a>
</div>
</div>

<div class="card an">
<div class="ch"><i class="fas fa-inbox" style="color:var(--gm);"></i><h2>Complaints Inbox</h2><span class="badge b-p" style="margin-left:6px;">{{ $complaints->total() }}</span></div>
<div id="inboxList">
@forelse($complaints as $c)
@php
    $anon = $c->is_anonymous;
    $studentName = $anon ? null : ($c->student->full_name ?? 'Unknown Student');
@endphp
<div class="inbox-row {{ $c->status==='Pending'?'st-pending':($c->status==='Resolved'?'st-resolved':'') }}" onclick="window.location='{{ route('admin.complaints.show',$c->id) }}'">
<div class="inbox-avatar">
@if($anon)<i class="fas fa-user-secret"></i>@else{{ strtoupper(substr($studentName,0,1)) }}@endif
</div>
<div class="inbox-mid">
<div class="inbox-subject">{{ $c->subject }}</div>
<div class="inbox-meta">
<span class="badge {{ $c->type==='Complaint'?'b-d':'b-i' }}" style="font-size:10px;">{{ $c->type }}</span>
@if($anon)<span style="font-style:italic;">Anonymous</span>@else<span>{{ $studentName }}</span>@endif
<span>· {{ $c->created_at->format('M d, Y g:i A') }}</span>
</div>
</div>
<div class="inbox-right">
<span class="badge {{ $c->status==='Pending'?'b-w':($c->status==='Under Review'?'b-i':($c->status==='Resolved'?'b-s':'b-gray')) }}">{{ $c->status }}</span>
<a href="{{ route('admin.complaints.show',$c->id) }}" class="btn btn-o btn-sm" onclick="event.stopPropagation();">View</a>
</div>
</div>
@empty
<div style="text-align:center;padding:50px 20px;color:var(--tm);"><i class="fas fa-inbox" style="font-size:36px;color:var(--bd);display:block;margin-bottom:10px;"></i>No complaints or reports have been filed yet.</div>
@endforelse
</div>
@if($complaints->hasPages())<div style="margin-top:14px;padding:0 20px 16px;">{{ $complaints->links() }}</div>@endif
</div>

@push('scripts')
<script>
(function(){
    var searchTimer = null;
    function applyFilters(){
        var params = new URLSearchParams();
        var s = document.getElementById('inboxSearch').value.trim();
        var t = document.getElementById('inboxType').value;
        var st = document.getElementById('inboxStatus').value;
        if (s) params.set('search', s);
        if (t) params.set('type', t);
        if (st) params.set('status', st);
        window.location = '{{ route('admin.complaints.index') }}' + (params.toString() ? '?' + params.toString() : '');
    }
    document.getElementById('inboxSearch').addEventListener('keyup', function(){
        clearTimeout(searchTimer);
        searchTimer = setTimeout(applyFilters, 400);
    });
    document.getElementById('inboxType').addEventListener('change', applyFilters);
    document.getElementById('inboxStatus').addEventListener('change', applyFilters);
})();
</script>
@endpush
@endsection
