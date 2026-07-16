@extends('admin.layouts.app')
@section('title','Students')
@section('page-title','Student Records')
@section('content')
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:14px;">
<form method="GET" style="display:flex;gap:8px;">
    <div style="position:relative;">
        <i class="fas fa-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--tm);font-size:12px;"></i>
        <input type="text" name="search" value="{{ request('search') }}" class="fc" placeholder="Search students..." style="padding-left:30px;width:200px;">
    </div>
    <button type="submit" class="btn btn-p btn-sm"><i class="fas fa-filter"></i> Search</button>
    <a href="{{ route('admin.students.index') }}" class="btn btn-o btn-sm">Clear</a>
</form>

<div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
    <a href="{{ route('admin.students.import-form') }}" class="btn btn-p btn-sm"><i class="fas fa-file-import"></i> Import Students</a>
    <a href="{{ route('admin.students.create') }}" class="btn btn-p btn-sm"><i class="fas fa-user-plus"></i> Add Student</a>
</div>
</div>


<div class="card an"><div class="ch"><i class="fas fa-users" style="color:var(--gm);"></i><h2>Students</h2><span class="badge b-p" style="margin-left:6px;">{{ $students->total() }}</span></div>
<div class="tw"><table><thead><tr><th>Student</th><th>Student ID</th><th>Course</th><th>Year</th><th>GWA</th><th>Enrollment</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
@forelse($students as $s)
<tr>

<td><div style="display:flex;align-items:center;gap:7px;"><div class="av av-s">{{ strtoupper(substr($s->first_name,0,1)) }}</div><div class="fws" style="font-size:13px;">{{ $s->full_name }}</div></div></td>
<td class="mono" style="font-size:12px;">{{ $s->student_id }}</td>
<td style="font-size:12px;color:var(--tm);">{{ Str::limit($s->course,25) }}</td>
<td style="font-size:12px;">{{ $s->year_level }}</td>
<td class="mono fwb" style="color:{{ (float)($s->gwa??0)<=1.75?'var(--gm)':((float)($s->gwa??0)<=2.25?'var(--warn)':'var(--danger)') }}">{{ number_format($s->gwa??0,2) }}</td>
<td><span class="badge {{ $s->enrollment_type==='Regular'?'b-p':'b-w' }}" style="font-size:10px;">{{ $s->enrollment_type }}</span></td>
<td><span class="badge {{ $s->status==='Active'?'b-s':'b-d' }}">{{ $s->status }}</span></td>
<td><div style="display:flex;gap:5px;"><a href="{{ route('admin.students.show',$s->id) }}" class="btn btn-o btn-sm btn-ic"><i class="fas fa-eye"></i></a><a href="{{ route('admin.students.edit',$s->id) }}" class="btn btn-o btn-sm btn-ic"><i class="fas fa-edit"></i></a></div></td>
</tr>
@empty
<tr><td colspan="8" style="text-align:center;padding:18px;color:var(--tm);">No students found.</td></tr>
@endforelse
</tbody></table></div>
@if($students->hasPages())<div style="padding:13px 18px;border-top:1px solid var(--bd);">{{ $students->links() }}</div>@endif
</div>
@endsection
