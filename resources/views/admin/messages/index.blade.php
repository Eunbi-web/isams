@extends('admin.layouts.app')
@section('title','Messages')
@section('page-title','Messages')
@section('page-sub','Messages sent to students')
@section('content')
<div class="card an" style="margin-bottom:16px;display:flex;align-items:center;gap:10px;padding:14px 20px;">
<div style="flex:1;"><i class="fas fa-envelope" style="color:var(--g);margin-right:8px;"></i><span class="fws">{{ $total }} message(s) sent · {{ $unread }} not yet read</span></div>
<a href="{{ route('admin.messages.create') }}" class="btn btn-p"><i class="fas fa-plus"></i> New Message</a>
</div>
<div class="card an">
<div class="ch"><i class="fas fa-envelope" style="color:var(--gm);"></i><h2>Sent Messages</h2><span class="badge b-p" style="margin-left:6px;">{{ $messages->total() }}</span></div>
<div class="tw"><table>
<thead><tr><th>Student Name</th><th>Student EDP</th><th>Subject</th><th>Sent On</th><th>Read Status</th></tr></thead>
<tbody>
@forelse($messages as $m)
<tr>
<td><div style="display:flex;align-items:center;gap:7px;"><div class="av av-s">{{ strtoupper(substr($m->student->first_name ?? 'S',0,1)) }}</div><div class="fws" style="font-size:13px;">{{ $m->student->full_name ?? '—' }}</div></div></td>
<td class="mono tm" style="font-size:12px;">{{ $m->student->student_id ?? '—' }}</td>
<td class="fws" style="max-width:320px;">{{ $m->subject }}</td>
<td class="tm" style="font-size:12px;">{{ $m->created_at->format('M d, Y g:i A') }}</td>
<td>@if($m->is_read)<span class="badge b-s"><i class="fas fa-check"></i> Opened {{ $m->read_at?->format('M d, g:i A') }}</span>@else<span class="badge b-w"><i class="fas fa-clock"></i> Not Yet Read</span>@endif</td>
</tr>
@empty
<tr><td colspan="5" style="text-align:center;padding:40px 20px;color:var(--tm);"><i class="fas fa-envelope" style="font-size:36px;color:var(--bd);display:block;margin-bottom:10px;"></i>No messages sent yet.</td></tr>
@endforelse
</tbody></table></div>
@if($messages->hasPages())<div style="margin-top:14px;padding:0 20px 16px;">{{ $messages->links() }}</div>@endif
</div>
@endsection
