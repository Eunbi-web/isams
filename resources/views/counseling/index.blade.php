@extends('layouts.app')
@section('title', 'Counseling')
@section('page-title', 'Counseling Services')
@section('page-subtitle', 'Manage student counseling sessions and appointments')

@section('content')
<div class="d-flex align-center justify-between mb-3">
    <div class="d-flex gap-2">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" class="form-control" placeholder="Search student or concern..." style="width:260px;">
        </div>
        <select class="form-control" style="width:160px;">
            <option value="">All Types</option>
            <option>Academic Stress</option>
            <option>Personal Issue</option>
            <option>Career Guidance</option>
            <option>Mental Health</option>
            <option>Family Concern</option>
            <option>Financial</option>
        </select>
        <select class="form-control" style="width:150px;">
            <option value="">All Status</option>
            <option>Pending</option>
            <option>Scheduled</option>
            <option>In Progress</option>
            <option>Completed</option>
            <option>Cancelled</option>
        </select>
    </div>
    <a href="{{ route('counseling.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> New Session
    </a>
</div>

<!-- Summary Cards -->
<div class="stats-grid mb-3" style="grid-template-columns:repeat(4,1fr);">
    <div class="stat-card animate delay-1">
        <div class="stat-icon teal"><i class="fas fa-clock"></i></div>
        <div class="stat-info"><div class="label">Pending</div><div class="value">18</div></div>
    </div>
    <div class="stat-card animate delay-2">
        <div class="stat-icon blue"><i class="fas fa-calendar-check"></i></div>
        <div class="stat-info"><div class="label">Scheduled</div><div class="value">24</div></div>
    </div>
    <div class="stat-card animate delay-3">
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        <div class="stat-info"><div class="label">Completed (Month)</div><div class="value">89</div></div>
    </div>
    <div class="stat-card animate delay-4">
        <div class="stat-icon red"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="stat-info"><div class="label">Urgent Cases</div><div class="value">5</div></div>
    </div>
</div>

<div class="card animate">
    <div class="card-header">
        <i class="fas fa-heart" style="color:#e87070;"></i>
        <h2>Counseling Sessions</h2>
        <div class="card-actions">
            <a href="{{ route('counseling.calendar') }}" class="btn btn-outline btn-sm"><i class="fas fa-calendar"></i> Calendar</a>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Session #</th>
                    <th>Student</th>
                    <th>Concern Type</th>
                    <th>Counselor</th>
                    <th>Date / Time</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach([
                    ['CS-001','Maria Santos','Academic Stress','Dr. Rivera','May 2, 2024 10:00 AM','Scheduled','High'],
                    ['CS-002','Juan Dela Cruz','Personal Issue','Ms. Torres','Apr 30, 2024 2:00 PM','Completed','Medium'],
                    ['CS-003','Ana Reyes','Career Guidance','Dr. Rivera','May 5, 2024 9:00 AM','Pending','Normal'],
                    ['CS-004','Mark Vilar','Anxiety / Mental Health','Dr. Santos','May 1, 2024 11:00 AM','In Progress','High'],
                    ['CS-005','Cindy Lim','Family Concern','Ms. Torres','May 3, 2024 1:00 PM','Scheduled','Medium'],
                    ['CS-006','Pedro Reyes','Financial Stress','Dr. Rivera','Apr 28, 2024 3:00 PM','Completed','Normal'],
                    ['CS-007','Elena Cruz','Relationship Issue','Ms. Torres','May 8, 2024 10:00 AM','Pending','Medium'],
                    ['CS-008','Tony Bautista','Academic Stress','Dr. Santos','May 6, 2024 2:00 PM','Scheduled','Normal'],
                ] as $i => $c)
                <tr>
                    <td class="font-mono" style="font-size:12px;color:var(--text-muted);">{{ $c[0] }}</td>
                    <td>
                        <div class="d-flex align-center gap-2">
                            <div class="avatar avatar-sm" style="background:linear-gradient(135deg,#e87070,#c0392b);">{{ substr($c[1],0,1) }}</div>
                            <span class="fw-semi">{{ $c[1] }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge {{ in_array($c[2],['Anxiety / Mental Health','Family Concern']) ? 'badge-danger' : 'badge-primary' }}">
                            {{ $c[2] }}
                        </span>
                    </td>
                    <td class="text-muted" style="font-size:13px;">{{ $c[3] }}</td>
                    <td class="text-muted font-mono" style="font-size:12px;">{{ $c[4] }}</td>
                    <td>
                        <span class="badge {{ $c[5]=='Completed' ? 'badge-success' : ($c[5]=='Pending' ? 'badge-warning' : ($c[5]=='Scheduled' ? 'badge-primary' : 'badge-info')) }}">
                            {{ $c[5] }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $c[6]=='High' ? 'badge-danger' : ($c[6]=='Medium' ? 'badge-warning' : 'badge-gray') }}">
                            {{ $c[6] }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="#" class="btn btn-outline btn-sm btn-icon" title="View"><i class="fas fa-eye"></i></a>
                            <a href="#" class="btn btn-outline btn-sm btn-icon" title="Edit"><i class="fas fa-edit"></i></a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
