@extends('admin.layouts.app')
@section('title','Scholarships')
@section('page-title','Scholarship Programs')
@section('page-sub','Manage all scholarship programs')
@section('content')
<div style="display:flex;justify-content:flex-end;margin-bottom:16px;"><a href="{{ route('admin.scholarships.create') }}" class="btn btn-p btn-sm"><i class="fas fa-plus"></i> Add Program</a></div>
<div class="card an">
<div class="ch"><i class="fas fa-award" style="color:var(--yd);"></i><h2>All Programs</h2><span class="badge b-p" style="margin-left:6px;">{{ $scholarships->total() }}</span></div>
<div class="tw"><table>
<thead><tr><th>Name</th><th>Type</th><th>Benefit</th><th>Slots</th><th>Applications</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
@forelse($scholarships as $sch)
<tr>
<td><div class="fws" style="font-size:13px;">{{ $sch->name }}</div></td>
<td><span class="badge {{ $sch->type==='Government'?'b-p':($sch->type==='Private'?'b-s':'b-i') }}">{{ $sch->type }}</span></td>
<td style="font-size:12px;color:var(--tm);">{{ Str::limit($sch->benefits??'—',40) }}</td>
<td class="mono fwb" style="color:var(--g);">{{ $sch->slots??'—' }}</td>
<td class="mono" style="color:var(--tm);">{{ $sch->applications_count??0 }}</td>
<td><span class="badge {{ $sch->status==='Active'?'b-s':'b-d' }}">{{ $sch->status }}</span></td>
<td><div style="display:flex;gap:5px;">
<a href="{{ route('admin.scholarships.show',$sch->id) }}" class="btn btn-o btn-sm btn-ic"><i class="fas fa-eye"></i></a>
<a href="{{ route('admin.scholarships.edit',$sch->id) }}" class="btn btn-o btn-sm btn-ic"><i class="fas fa-edit"></i></a>
<form method="POST" action="{{ route('admin.scholarships.destroy',$sch->id) }}">@csrf @method('DELETE')<button class="btn btn-d btn-sm btn-ic" onclick="return confirm('Delete this scholarship?')"><i class="fas fa-trash"></i></button></form>
</div></td>
</tr>
@empty
<tr><td colspan="7" style="text-align:center;padding:20px;color:var(--tm);">No scholarships yet. <a href="{{ route('admin.scholarships.create') }}" style="color:var(--gm);">Add one</a></td></tr>
@endforelse
</tbody></table></div>
@if($scholarships->hasPages())<div style="padding:13px 18px;border-top:1px solid var(--bd);">{{ $scholarships->links() }}</div>@endif
</div>
@endsection
