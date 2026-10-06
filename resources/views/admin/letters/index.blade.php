@extends('admin.layouts.app')
@section('title','Confiscated Item Letters')
@section('page-title','Confiscated Item Letters')
@section('page-sub','Review student letters requesting the release of confiscated items')
@push('styles')
<style>
.inbox-row{display:flex;align-items:center;padding:14px 18px;border-bottom:1px solid var(--bd);cursor:pointer;transition:background .15s;border-left:3px solid transparent;}
.inbox-row:hover{background:var(--bg);}
.inbox-row.st-submitted{border-left:3px solid var(--y);}
.inbox-row.st-approved{border-left:3px solid var(--gm);}
.inbox-row.st-rejected{border-left:3px solid var(--danger);}
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
<div class="sc an d1"><div class="si t"><i class="fas fa-box"></i></div><div class="sv"><div class="lbl">Total Letters</div><div class="val">{{ $stats['total'] }}</div></div></div>
<div class="sc an d2"><div class="si y"><i class="fas fa-clock"></i></div><div class="sv"><div class="lbl">Submitted</div><div class="val">{{ $stats['submitted'] }}</div></div></div>
<div class="sc an d3"><div class="si o"><i class="fas fa-search"></i></div><div class="sv"><div class="lbl">Under Review</div><div class="val">{{ $stats['under_review'] }}</div></div></div>
<div class="sc an d4"><div class="si g"><i class="fas fa-check-circle"></i></div><div class="sv"><div class="lbl">Approved</div><div class="val">{{ $stats['approved'] }}</div></div></div>
</div>

<div class="card mb3 an" style="padding:14px 18px;">
<div style="display:flex;gap:9px;flex-wrap:wrap;align-items:center;">
<input type="text" id="inboxSearch" class="finput" style="flex:1;min-width:220px;" placeholder="Search by item or student name / EDP" value="{{ $filters['search'] }}">
<select id="inboxStatus" class="finput" style="width:170px;">
<option value="">All Status</option>
@foreach(['Submitted','Under Review','Approved','Rejected'] as $st)
<option value="{{ $st }}" {{ $filters['status']===$st?'selected':'' }}>{{ $st }}</option>
@endforeach
</select>
<a href="{{ route('admin.letters.index') }}" class="btn btn-o btn-sm"><i class="fas fa-times"></i> Clear Filters</a>
</div>
</div>

<div class="card an">
<div class="ch"><i class="fas fa-box" style="color:var(--gm);"></i><h2>Letters Inbox</h2><span class="badge b-p" style="margin-left:6px;">{{ $letters->total() }}</span></div>
<div id="inboxList">
@forelse($letters as $l)
@php $studentName = $l->student->full_name ?? 'Unknown Student'; @endphp
<div class="inbox-row {{ $l->status==='Submitted'?'st-submitted':($l->status==='Approved'?'st-approved':($l->status==='Rejected'?'st-rejected':'')) }}" onclick="window.location='{{ route('admin.letters.show',$l->id) }}'">
<div class="inbox-avatar">{{ strtoupper(substr($studentName,0,1)) }}</div>
<div class="inbox-mid">
<div class="inbox-subject">{{ $l->item_description }}</div>
<div class="inbox-meta">
<span>{{ $studentName }}</span>
<span>· EDP: <span class="mono">{{ edp_short($l->student->student_id) }}</span></span>
<span>· Submitted {{ $l->created_at->format('M d, Y g:i A') }}</span>
</div>
</div>
<div class="inbox-right">
<span class="badge {{ $l->status==='Submitted'?'b-w':($l->status==='Under Review'?'b-i':($l->status==='Approved'?'b-s':'b-d')) }}">{{ $l->status }}</span>
<a href="{{ route('admin.letters.show',$l->id) }}" class="btn btn-o btn-sm" onclick="event.stopPropagation();">Review</a>
</div>
</div>
@empty
<div style="text-align:center;padding:50px 20px;color:var(--tm);"><i class="fas fa-box" style="font-size:36px;color:var(--bd);display:block;margin-bottom:10px;"></i>No confiscated item letters have been submitted yet.</div>
@endforelse
</div>
@if($letters->hasPages())<div style="margin-top:14px;padding:0 20px 16px;">{{ $letters->links() }}</div>@endif
</div>

@push('scripts')
<script>
(function(){
    var searchTimer = null;
    function applyFilters(){
        var params = new URLSearchParams();
        var s = document.getElementById('inboxSearch').value.trim();
        var st = document.getElementById('inboxStatus').value;
        if (s) params.set('search', s);
        if (st) params.set('status', st);
        window.location = '{{ route('admin.letters.index') }}' + (params.toString() ? '?' + params.toString() : '');
    }
    document.getElementById('inboxSearch').addEventListener('keyup', function(){
        clearTimeout(searchTimer);
        searchTimer = setTimeout(applyFilters, 400);
    });
    document.getElementById('inboxStatus').addEventListener('change', applyFilters);
})();
</script>
@endpush
@endsection
