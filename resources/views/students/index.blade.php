@extends('layouts.app')
@section('title', 'Students')
@section('page-title', 'Student Records')
@section('page-subtitle', 'Manage enrolled students and their information')

@section('content')
<div class="d-flex align-center justify-between mb-3">
    <div class="d-flex gap-2">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" class="form-control" placeholder="Search students..." id="searchInput" style="width:280px;">
        </div>
        <select class="form-control" style="width:180px;" id="courseFilter">
            <option value="">All Courses</option>
            <option>BS Computer Science</option>
            <option>BS Education</option>
            <option>BS Nursing</option>
            <option>BS Engineering</option>
            <option>BS Business Admin</option>
            <option>AB Communication</option>
        </select>
        <select class="form-control" style="width:150px;" id="yearFilter">
            <option value="">All Year Levels</option>
            <option>1st Year</option>
            <option>2nd Year</option>
            <option>3rd Year</option>
            <option>4th Year</option>
        </select>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('students.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Enroll Student
        </a>
        <a href="{{ route('students.export') }}" class="btn btn-outline">
            <i class="fas fa-file-excel"></i> Export
        </a>
    </div>
</div>

<div class="card animate">
    <div class="card-header">
        <i class="fas fa-user-graduate" style="color:var(--primary-light);"></i>
        <h2>All Students</h2>
        <span class="badge badge-primary" style="margin-left:6px;">{{ $students->total() ?? 4280 }} total</span>
        <div class="card-actions">
            <div style="font-size:12px;color:var(--text-muted);">
                Academic Year: <strong>2023–2024</strong>
            </div>
        </div>
    </div>
    <div class="table-wrap">
        <table id="studentsTable">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Student ID</th>
                    <th>Course & Year</th>
                    <th>Contact</th>
                    <th>GWA</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students ?? [] as $student)
                <tr>
                    <td>
                        <div class="d-flex align-center gap-2">
                            <div class="avatar avatar-md">{{ strtoupper(substr($student->first_name,0,1)) }}</div>
                            <div>
                                <div class="fw-semi">{{ $student->full_name }}</div>
                                <div class="text-muted" style="font-size:11px;">{{ $student->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="font-mono" style="font-size:13px;">{{ $student->student_id }}</td>
                    <td><div>{{ $student->course }}</div><div class="text-muted" style="font-size:12px;">{{ $student->year_level }}</div></td>
                    <td class="text-muted" style="font-size:13px;">{{ $student->contact_number }}</td>
                    <td>
                        <span class="fw-semi {{ ($student->gwa ?? 1.5) <= 1.5 ? 'text-success' : (($student->gwa ?? 1.5) <= 2.5 ? 'text-primary' : 'text-danger') }}">
                            {{ number_format($student->gwa ?? 1.75, 2) }}
                        </span>
                    </td>
                    <td>
                        @if(($student->status ?? 'Active') == 'Active')
                            <span class="badge badge-success">Active</span>
                        @elseif($student->status == 'Irregular')
                            <span class="badge badge-warning">Irregular</span>
                        @else
                            <span class="badge badge-gray">{{ $student->status }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('students.show', $student->id) }}" class="btn btn-outline btn-sm btn-icon" title="View"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('students.edit', $student->id) }}" class="btn btn-outline btn-sm btn-icon" title="Edit"><i class="fas fa-edit"></i></a>
                        </div>
                    </td>
                </tr>
                @empty
                @foreach([
                    ['M','Maria Santos','2024-0001','BS Computer Science','3rd Year','maria@isams.edu.ph','09171234567','1.50','Active'],
                    ['J','Juan Dela Cruz','2024-0002','BS Education','2nd Year','juan@isams.edu.ph','09181234567','1.75','Active'],
                    ['A','Ana Reyes','2024-0003','BS Nursing','4th Year','ana@isams.edu.ph','09191234567','1.25','Active'],
                    ['C','Carlos Mendoza','2024-0004','BS Engineering','1st Year','carlos@isams.edu.ph','09201234567','2.00','Irregular'],
                    ['R','Rosa Garcia','2024-0005','BS Business Admin','3rd Year','rosa@isams.edu.ph','09211234567','1.60','Active'],
                    ['P','Pedro Lim','2024-0006','AB Communication','2nd Year','pedro@isams.edu.ph','09221234567','1.80','Active'],
                    ['E','Elena Cruz','2024-0007','BS Computer Science','4th Year','elena@isams.edu.ph','09231234567','1.40','Active'],
                    ['T','Tony Villanueva','2024-0008','BS Nursing','3rd Year','tony@isams.edu.ph','09241234567','2.25','Active'],
                ] as $s)
                <tr>
                    <td>
                        <div class="d-flex align-center gap-2">
                            <div class="avatar avatar-md">{{ $s[0] }}</div>
                            <div>
                                <div class="fw-semi">{{ $s[1] }}</div>
                                <div class="text-muted" style="font-size:11px;">{{ $s[6] }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="font-mono" style="font-size:13px;">{{ $s[2] }}</td>
                    <td><div>{{ $s[3] }}</div><div class="text-muted" style="font-size:12px;">{{ $s[4] }}</div></td>
                    <td class="text-muted" style="font-size:13px;">{{ $s[5] }}</td>
                    <td><span class="fw-semi {{ $s[7] <= 1.5 ? 'text-success' : 'text-primary' }}">{{ $s[7] }}</span></td>
                    <td>
                        <span class="badge {{ $s[8]=='Active' ? 'badge-success' : 'badge-warning' }}">{{ $s[8] }}</span>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="#" class="btn btn-outline btn-sm btn-icon" title="View"><i class="fas fa-eye"></i></a>
                            <a href="#" class="btn btn-outline btn-sm btn-icon" title="Edit"><i class="fas fa-edit"></i></a>
                        </div>
                    </td>
                </tr>
                @endforeach
                @endforelse
            </tbody>
        </table>
    </div>
    @if(isset($students) && $students->hasPages())
    <div style="padding:16px 22px;border-top:1px solid var(--border);">
        {{ $students->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
document.getElementById('searchInput').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#studentsTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
@endpush
@endsection
