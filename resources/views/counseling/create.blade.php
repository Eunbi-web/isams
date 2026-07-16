@extends('layouts.app')
@section('title', 'New Counseling Session')
@section('page-title', 'New Counseling Session')
@section('page-subtitle', 'Create a counseling session or appointment')

@section('content')
<div style="max-width:760px;">
<form method="POST" action="{{ route('counseling.store') }}">
    @csrf
    <div class="card animate mb-3">
        <div class="card-header">
            <i class="fas fa-heart" style="color:#e87070;"></i>
            <h2>Session Information</h2>
        </div>
        <div class="card-body">
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Student <span style="color:var(--danger)">*</span></label>
                    <select name="student_id" class="form-control" required>
                        <option value="">Select student...</option>
                        @forelse($students ?? [] as $student)
                        <option value="{{ $student->id }}">{{ $student->full_name }} — {{ $student->student_id }}</option>
                        @empty
                        <option value="1">Maria Santos — 2024-0001</option>
                        <option value="2">Juan Dela Cruz — 2024-0002</option>
                        @endforelse
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Counselor</label>
                    <select name="counselor_id" class="form-control">
                        <option value="">Unassigned</option>
                        @forelse($counselors ?? [] as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @empty
                        <option value="2">Dr. Maria Rivera</option>
                        <option value="3">Ms. Ana Torres</option>
                        @endforelse
                    </select>
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Concern Type <span style="color:var(--danger)">*</span></label>
                    <select name="concern_type" class="form-control" required>
                        <option value="">Select type</option>
                        @foreach(['Academic Stress','Personal Issue','Career Guidance','Mental Health / Anxiety','Family Concern','Financial Stress','Relationship Issue','Substance Abuse Concern','Suicidal Ideation (Urgent)','General Wellness'] as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Priority Level</label>
                    <select name="priority" class="form-control">
                        <option value="Normal">Normal</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                        <option value="Urgent">Urgent</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Concern Details</label>
                <textarea name="concern_detail" class="form-control" rows="4" placeholder="Brief description of the student's concern...">{{ old('concern_detail') }}</textarea>
            </div>
            <div class="grid-3">
                <div class="form-group">
                    <label class="form-label">Session Date</label>
                    <input type="date" name="session_date" class="form-control" value="{{ old('session_date') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Session Time</label>
                    <input type="time" name="session_time" class="form-control" value="{{ old('session_time') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="Pending">Pending</option>
                        <option value="Scheduled">Scheduled</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Create Session</button>
        <a href="{{ route('counseling.index') }}" class="btn btn-outline"><i class="fas fa-times"></i> Cancel</a>
    </div>
</form>
</div>
@endsection
