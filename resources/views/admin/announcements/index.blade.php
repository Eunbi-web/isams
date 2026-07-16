@extends('admin.layouts.app')
@section('title','Announcements')
@section('page-title','Announcements')
@section('content')
<div style="display:flex;justify-content:flex-end;margin-bottom:14px;"><a href="{{ route('admin.announcements.create') }}" class="btn btn-p btn-sm"><i class="fas fa-plus"></i> New Announcement</a></div>
<div class="card an"><div class="ch"><i class="fas fa-bullhorn" style="color:var(--gm);"></i><h2>All Announcements</h2></div>
<div class="tw"><table><thead><tr><th>Title</th><th>Type</th><th>Priority</th><th>Date</th><th>Actions</th></tr></thead>
<tbody>
@forelse($announcements as $ann)
<tr>
<td class="fws" style="font-size:13px;">{{ Str::limit($ann->title,50) }}</td>
<td><span class="badge b-p" style="font-size:10px;">{{ $ann->type }}</span></td>
<td><span class="badge {{ $ann->priority==='Urgent'?'b-d':($ann->priority==='High'?'b-w':'b-gray') }}">{{ $ann->priority }}</span></td>
<td class="mono tm" style="font-size:11px;">{{ $ann->created_at->format('M d, Y') }}</td>
<td><div style="display:flex;gap:5px;">
<a href="{{ route('admin.announcements.edit',$ann->id) }}" class="btn btn-o btn-sm btn-ic"><i class="fas fa-edit"></i></a>
<form method="POST" action="{{ route('admin.announcements.destroy',$ann->id) }}">@csrf @method('DELETE')<button class="btn btn-d btn-sm btn-ic" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></button></form>
</div></td>
</tr>
@empty
<tr><td colspan="5" style="text-align:center;padding:16px;color:var(--tm);">No announcements.</td></tr>
@endforelse
</tbody></table></div>
@if($announcements->hasPages())<div style="padding:13px 18px;border-top:1px solid var(--bd);">{{ $announcements->links() }}</div>@endif
</div>
@endsection
