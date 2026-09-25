@extends('student.layouts.app')
@section('title','Messages')
@section('page-title','Messages')
@section('page-sub','Messages from the Student Affairs Office')
@section('content')
<div class="card an">
<div class="ch"><i class="fas fa-envelope" style="color:var(--gm);"></i><h2>Inbox</h2><span class="badge b-p" style="margin-left:6px;">{{ $messages->total() }}</span></div>
<div class="tw"><table>
<thead><tr><th>Subject</th><th>From</th><th>Sent On</th><th>Status</th><th></th></tr></thead>
<tbody>
@forelse($messages as $m)
<tr>
<td class="{{ $m->is_read?'':'fwb' }}" style="max-width:320px;">{{ $m->subject }}</td>
<td class="tm" style="font-size:12px;">Admin - Student Affairs Office</td>
<td class="tm" style="font-size:12px;">{{ $m->created_at->format('M d, Y g:i A') }}</td>
<td>@if($m->is_read)<span class="badge b-s">Read</span>@else<span class="badge b-w">Unread</span>@endif</td>
<td><a href="{{ route('student.messages.show',$m->id) }}" class="btn btn-o btn-sm"><i class="fas fa-eye"></i> View</a></td>
</tr>
@empty
<tr><td colspan="5" style="text-align:center;padding:40px 20px;color:var(--tm);"><i class="fas fa-envelope-open" style="font-size:36px;color:var(--bd);display:block;margin-bottom:10px;"></i>No messages from the admin yet.</td></tr>
@endforelse
</tbody></table></div>
@if($messages->hasPages())<div style="margin-top:14px;padding:0 20px 16px;">{{ $messages->links() }}</div>@endif
</div>
@endsection
