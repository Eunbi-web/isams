@extends('layouts.app')
@section('title', 'File Discipline Case')
@section('page-title', 'File Discipline Case')
@section('page-subtitle', 'Create a new student discipline case')

@section('content')
<div style="max-width:760px;">
<form method="POST" action="{{ route('discipline.store') }}">
    @csrf
    <div class="card animate mb-3">
        <div class="card-header">
            <i class="fas fa-gavel" style="color:var(--danger);"></i>
            <h2>Case Details</h2>
        </div>
        <div class="card-body">
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Student <span style="color:var(--danger)">*</span></label>
                    <select name="student_id" class="form-control" required>
                        <option value="">Search and select student...</option>
                        @forelse($students ?? [] as $student)
                        <option value="{{ $student->id }}">{{ $student->full_name }} — {{ $student->student_id }}</option>
                        @empty
                        <option value="1">Maria Santos — 2024-0001</option>
                        <option value="2">Juan Dela Cruz — 2024-0002</option>
                        @endforelse
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Violation Type <span style="color:var(--danger)">*</span></label>
                    <select name="violation_type" class="form-control" required>
                        <option value="">Select violation</option>
                        @foreach(['Academic Dishonesty','Property Damage','Minor Misconduct','Major Misconduct','Dress Code Violation','Substance Abuse','Harassment','Bullying','Unauthorized Absence','Insubordination'] as $v)
                        <option value="{{ $v }}">{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Incident Date <span style="color:var(--danger)">*</span></label>
                    <input type="date" name="incident_date" class="form-control" value="{{ old('incident_date', date('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Initial Status</label>
                    <select name="status" class="form-control">
                        <option value="Under Investigation">Under Investigation</option>
                        <option value="Pending Documentation">Pending Documentation</option>
                        <option value="Hearing Scheduled">Hearing Scheduled</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Incident Description</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Provide a detailed description of the incident...">{{ old('description') }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Witnesses (if any)</label>
                <textarea name="witnesses" class="form-control" rows="2" placeholder="Names of witnesses...">{{ old('witnesses') }}</textarea>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Hearing Date (if scheduled)</label>
                    <input type="date" name="hearing_date" class="form-control" value="{{ old('hearing_date') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Proposed Sanction</label>
                    <select name="sanction" class="form-control">
                        <option value="">None yet</option>
                        @foreach(['Written Warning','Verbal Warning','Probation','Suspension (1-3 days)','Suspension (1 week)','Suspension (1 month)','Community Service','Dismissal'] as $sanction)
                        <option>{{ $sanction }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="fas fa-file-alt"></i> File Case</button>
        <a href="{{ route('discipline.index') }}" class="btn btn-outline"><i class="fas fa-times"></i> Cancel</a>
    </div>
</form>
</div>
@endsection
