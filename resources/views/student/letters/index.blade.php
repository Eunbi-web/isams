@extends('student.layouts.app')
@section('title','Confiscated Item Letters')
@section('page-title','Confiscated Item Letters')
@section('page-sub','Request the release of your confiscated items by submitting a formal letter')
@section('content')
<div class="alert al-i an"><i class="fas fa-info-circle"></i><span>Once you submit your letter, <strong>Sir RB</strong> will review it. You may claim your item at the SAO Office after your letter has been approved.</span></div>
<div class="card an" style="margin-bottom:16px;display:flex;align-items:center;gap:10px;padding:14px 20px;">
<div style="flex:1;"><i class="fas fa-box" style="color:var(--g);margin-right:8px;"></i><span class="fws">Letters requesting the release of confiscated items</span></div>
<a href="{{ route('student.letters.create') }}" class="btn btn-p"><i class="fas fa-plus"></i> Write New Letter</a>
</div>
<div class="card an">
<div class="ch"><i class="fas fa-box" style="color:var(--gm);"></i><h2>My Letters</h2><span class="badge b-p" style="margin-left:6px;">{{ $letters->total() }}</span></div>
<div class="tw"><table>
<thead><tr><th>No.</th><th>Item Description</th><th>Date Confiscated</th><th>Date Submitted</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
@forelse($letters as $l)
<tr>
<td class="mono tm" style="font-size:12px;">{{ $letters->firstItem() + $loop->index }}</td>
<td class="fws" style="max-width:280px;">{{ $l->item_description }}</td>
<td class="tm" style="font-size:12px;">{{ $l->date_confiscated ? $l->date_confiscated->format('M d, Y') : '—' }}</td>
<td class="tm" style="font-size:12px;">{{ $l->created_at->format('M d, Y') }}</td>
<td><span class="badge {{ $l->status==='Submitted'?'b-w':($l->status==='Under Review'?'b-i':($l->status==='Approved'?'b-s':'b-d')) }}">{{ $l->status }}</span></td>
<td><a href="{{ route('student.letters.show',$l->id) }}" class="btn btn-o btn-sm"><i class="fas fa-eye"></i> View</a></td>
</tr>
@empty
<tr><td colspan="6" style="text-align:center;padding:40px 20px;color:var(--tm);"><i class="fas fa-box-open" style="font-size:36px;color:var(--bd);display:block;margin-bottom:10px;"></i>You have not submitted any confiscated item letters yet. Click <strong>Write New Letter</strong> to get started.</td></tr>
@endforelse
</tbody></table></div>
@if($letters->hasPages())<div style="margin-top:14px;padding:0 20px 16px;">{{ $letters->links() }}</div>@endif
</div>
@endsection
