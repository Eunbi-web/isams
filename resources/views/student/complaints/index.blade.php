@extends('student.layouts.app')
@section('title','My Complaints and Reports')
@section('page-title','My Complaints and Reports')
@section('page-sub','Track the status of your submitted complaints and reports')
@section('content')
<div class="card an" style="margin-bottom:16px;display:flex;align-items:center;gap:10px;padding:14px 20px;">
<div style="flex:1;"><i class="fas fa-exclamation-circle" style="color:var(--g);margin-right:8px;"></i><span class="fws">Complaints and Reports filed with the Student Affairs Office</span></div>
<a href="{{ route('student.complaints.create') }}" class="btn btn-p"><i class="fas fa-plus"></i> File New Complaint</a>
</div>
<div class="card an">
<div class="ch"><i class="fas fa-inbox" style="color:var(--gm);"></i><h2>My Complaints and Reports</h2><span class="badge b-p" style="margin-left:6px;">{{ $complaints->total() }}</span></div>
<div class="tw"><table>
<thead><tr><th>No.</th><th>Type</th><th>Subject</th><th>Submitted On</th><th>Anonymous</th><th>Status</th><th>Admin Reply</th></tr></thead>
<tbody>
@forelse($complaints as $c)
<tr>
<td class="mono tm" style="font-size:12px;">{{ $complaints->firstItem() + $loop->index }}</td>
<td><span class="badge {{ $c->type==='Complaint'?'b-d':'b-i' }}">{{ $c->type }}</span></td>
<td class="fws" style="max-width:260px;">{{ $c->subject }}</td>
<td class="tm" style="font-size:12px;">{{ $c->created_at->format('M d, Y') }}</td>
<td>@if($c->is_anonymous)<span class="fwb" style="color:var(--gm);">Yes</span>@else<span class="tm">No</span>@endif</td>
<td><span class="badge {{ $c->status==='Pending'?'b-w':($c->status==='Under Review'?'b-i':($c->status==='Resolved'?'b-s':'b-gray')) }}">{{ $c->status }}</span></td>
<td class="tm" style="font-size:12px;max-width:220px;">@if($c->admin_reply){{ Str::limit($c->admin_reply,60) }}@else<span class="tm">—</span>@endif</td>
</tr>
@empty
<tr><td colspan="7" style="text-align:center;padding:40px 20px;color:var(--tm);"><i class="fas fa-inbox" style="font-size:36px;color:var(--bd);display:block;margin-bottom:10px;"></i>You have not filed any complaints or reports yet.</td></tr>
@endforelse
</tbody></table></div>
@if($complaints->hasPages())<div style="margin-top:14px;padding:0 20px 16px;">{{ $complaints->links() }}</div>@endif
</div>
@endsection
