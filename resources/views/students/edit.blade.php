@extends('layouts.app')
@section('title', 'Edit Student')
@section('page-title', 'Edit Student Record')
@section('content')
<div style="max-width:860px;">
    <div class="alert alert-info animate"><i class="fas fa-info-circle"></i> Edit form mirrors the enrollment form with pre-filled data.</div>
    <div class="card animate">
        <div class="card-body">
            <p>This view reuses the same structure as <code>students/create.blade.php</code> but pre-fills values from <code>$student</code>.</p>
            <div class="d-flex gap-2 mt-2">
                <a href="{{ isset($student) ? route('students.show', $student->id) : route('students.index') }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
        </div>
    </div>
</div>
@endsection
