@extends('admin.layouts.app')
@section('title','Students')
@section('page-title','Student Records')
@section('content')
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:14px;">
<form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
    <div style="position:relative;">
        <i class="fas fa-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--tm);font-size:12px;"></i>
        <input type="text" name="search" value="{{ request('search') }}" class="fc" placeholder="Search students..." style="padding-left:30px;width:200px;">
    </div>
    <select name="course" class="fc" style="width:170px;">
        <option value="">All Courses</option>
        @foreach($courses as $c)
        <option value="{{ $c }}" {{ request('course')===$c?'selected':'' }}>{{ Str::limit($c,26) }}</option>
        @endforeach
    </select>
    <select name="year_level" class="fc" style="width:120px;">
        <option value="">All Years</option>
        @foreach($years as $y)
        <option value="{{ $y }}" {{ request('year_level')===$y?'selected':'' }}>{{ $y }}</option>
        @endforeach
    </select>
    <select name="enrollment_type" class="fc" style="width:140px;">
        <option value="">All Enrollment</option>
        <option value="Regular" {{ request('enrollment_type')==='Regular'?'selected':'' }}>Regular</option>
        <option value="Irregular" {{ request('enrollment_type')==='Irregular'?'selected':'' }}>Irregular</option>
    </select>
    <select name="status" class="fc" style="width:120px;">
        <option value="">All Status</option>
        <option value="Active" {{ request('status')==='Active'?'selected':'' }}>Active</option>
        <option value="Inactive" {{ request('status')==='Inactive'?'selected':'' }}>Inactive</option>
    </select>
    <button type="submit" class="btn btn-p btn-sm"><i class="fas fa-filter"></i> Filter</button>
    <a href="{{ route('admin.students.index') }}" class="btn btn-o btn-sm">Clear</a>
</form>

<div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
    <a href="{{ route('admin.students.import-form') }}" class="btn btn-p btn-sm"><i class="fas fa-file-import"></i> Import Students</a>
    <a href="{{ route('admin.students.create') }}" class="btn btn-p btn-sm"><i class="fas fa-user-plus"></i> Add Student</a>
</div>
</div>


<div class="card an"><div class="ch"><i class="fas fa-users" style="color:var(--gm);"></i><h2>Students</h2><span class="badge b-p" style="margin-left:6px;">{{ $students->total() }}</span></div>
<div class="tw"><table><thead><tr><th>EDP Number</th><th>Last Name</th><th>First Name</th><th>Middle Name</th><th>Department</th><th>Sex</th><th>Year Level</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
@forelse($students as $s)
<tr>
<td class="mono" style="font-size:12px;">{{ $s->student_id }}</td>
<td class="fws" style="font-size:13px;">{{ $s->last_name }}</td>
<td style="font-size:13px;">{{ $s->first_name }}</td>
<td style="font-size:13px;color:var(--tm);">{{ $s->middle_name ?? '—' }}</td>
<td style="font-size:12px;color:var(--tm);">{{ Str::limit($s->department ?: $s->course, 25) }}</td>
<td style="font-size:12px;">{{ $s->sex ?? '—' }}</td>
<td style="font-size:12px;">{{ $s->year_level }}</td>
<td><span class="badge {{ $s->status==='Active'?'b-s':'b-d' }}">{{ $s->status }}</span></td>
<td><div style="display:flex;gap:5px;"><a href="{{ route('admin.students.show',$s->id) }}" class="btn btn-o btn-sm btn-ic"><i class="fas fa-eye"></i></a><a href="{{ route('admin.students.edit',$s->id) }}" class="btn btn-o btn-sm btn-ic"><i class="fas fa-edit"></i></a></div></td>
</tr>
@empty
<tr><td colspan="9" style="text-align:center;padding:18px;color:var(--tm);">No students found.</td></tr>
@endforelse
</tbody></table></div>
@if($students->hasPages())<div style="padding:13px 18px;border-top:1px solid var(--bd);">{{ $students->links() }}</div>@endif
</div>
@endsection
