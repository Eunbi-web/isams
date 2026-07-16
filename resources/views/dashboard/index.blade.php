@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Welcome back, ' . (auth()->user()->name ?? 'Administrator'))
@section('school', 'University')

@section('content')
<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card animate delay-1">
        <div class="stat-icon blue"><i class="fas fa-user-graduate"></i></div>
        <div class="stat-info">
            <div class="label">Total Students</div>
            <div class="value">{{ number_format($stats['total_students'] ?? 4280) }}</div>
            <div class="change"><i class="fas fa-arrow-up"></i> +124 this semester</div>
        </div>
    </div>
    <div class="stat-card animate delay-2">
        <div class="stat-icon green"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <div class="label">Organizations</div>
            <div class="value">{{ $stats['total_organizations'] ?? 38 }}</div>
            <div class="change"><i class="fas fa-arrow-up"></i> +3 this year</div>
        </div>
    </div>
    <div class="stat-card animate delay-3">
        <div class="stat-icon gold"><i class="fas fa-award"></i></div>
        <div class="stat-info">
            <div class="label">Scholars</div>
            <div class="value">{{ number_format($stats['total_scholars'] ?? 892) }}</div>
            <div class="change"><i class="fas fa-arrow-up"></i> +45 new grantees</div>
        </div>
    </div>
    <div class="stat-card animate delay-4">
        <div class="stat-icon teal"><i class="fas fa-heart"></i></div>
        <div class="stat-info">
            <div class="label">Counseling Sessions</div>
            <div class="value">{{ $stats['total_counseling'] ?? 156 }}</div>
            <div class="change"><i class="fas fa-arrow-up"></i> +12 this month</div>
        </div>
    </div>
    <div class="stat-card animate delay-5">
        <div class="stat-icon red"><i class="fas fa-gavel"></i></div>
        <div class="stat-info">
            <div class="label">Active Cases</div>
            <div class="value">{{ $stats['active_cases'] ?? 14 }}</div>
            <div class="change down"><i class="fas fa-arrow-down"></i> -3 from last month</div>
        </div>
    </div>
    <div class="stat-card animate delay-6">
        <div class="stat-icon purple"><i class="fas fa-calendar-check"></i></div>
        <div class="stat-info">
            <div class="label">Events This Month</div>
            <div class="value">{{ $stats['events_month'] ?? 22 }}</div>
            <div class="change"><i class="fas fa-arrow-up"></i> +5 upcoming</div>
        </div>
    </div>
</div>

<div class="grid-2" style="gap:22px;">
    <!-- Recent Students -->
    <div class="card animate">
        <div class="card-header">
            <i class="fas fa-user-graduate" style="color:var(--primary-light);"></i>
            <h2>Recently Enrolled Students</h2>
            <div class="card-actions">
                <a href="{{ route('students.index') }}" class="btn btn-outline btn-sm">View All</a>
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Year</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent_students ?? [] as $student)
                    <tr>
                        <td>
                            <div class="d-flex align-center gap-2">
                                <div class="avatar avatar-sm">{{ strtoupper(substr($student->first_name,0,1)) }}</div>
                                <div>
                                    <div class="fw-semi">{{ $student->full_name }}</div>
                                    <div class="text-muted" style="font-size:11px;">{{ $student->student_id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-muted">{{ $student->course }}</td>
                        <td>{{ $student->year_level }}</td>
                        <td><span class="badge badge-success">Active</span></td>
                    </tr>
                    @empty
                    @foreach([
                        ['M','Maria Santos','2024-001','BS Computer Science','3rd Year'],
                        ['J','Juan Dela Cruz','2024-002','BS Education','2nd Year'],
                        ['A','Ana Reyes','2024-003','BS Nursing','4th Year'],
                        ['C','Carlos Mendoza','2024-004','BS Engineering','1st Year'],
                        ['R','Rosa Garcia','2024-005','BS Business Admin','3rd Year'],
                    ] as $s)
                    <tr>
                        <td>
                            <div class="d-flex align-center gap-2">
                                <div class="avatar avatar-sm">{{ $s[0] }}</div>
                                <div>
                                    <div class="fw-semi">{{ $s[1] }}</div>
                                    <div class="text-muted" style="font-size:11px;">{{ $s[2] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-muted">{{ $s[3] }}</td>
                        <td>{{ $s[4] }}</td>
                        <td><span class="badge badge-success">Active</span></td>
                    </tr>
                    @endforeach
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Upcoming Events -->
    <div class="card animate">
        <div class="card-header">
            <i class="fas fa-calendar-alt" style="color:var(--accent);"></i>
            <h2>Upcoming Events</h2>
            <div class="card-actions">
                <a href="{{ route('events.create') }}" class="btn btn-accent btn-sm"><i class="fas fa-plus"></i> Add</a>
            </div>
        </div>
        <div class="card-body">
            @php
            $upcomingEvents = $upcoming_events ?? [
                ['title'=>'Leadership Summit 2024','date'=>'Apr 25, 2024','type'=>'Leadership','color'=>'primary'],
                ['title'=>'Mental Health Week','date'=>'Apr 28–May 3','type'=>'Wellness','color'=>'success'],
                ['title'=>'Organizations Fair','date'=>'May 5, 2024','type'=>'Activity','color'=>'info'],
                ['title'=>'Scholarship Application Day','date'=>'May 10, 2024','type'=>'Academic','color'=>'warning'],
                ['title'=>'Intramurals 2024','date'=>'May 15–18, 2024','type'=>'Sports','color'=>'danger'],
            ];
            @endphp
            @foreach($upcomingEvents as $event)
            <div style="display:flex;align-items:center;gap:14px;padding:12px 0;border-bottom:1px solid var(--border);">
                <div style="width:44px;height:44px;border-radius:10px;background:{{ $event['color']=='primary' ? '#e8f0fb' : ($event['color']=='success' ? '#e2f5ec' : ($event['color']=='info' ? '#e0f3f8' : ($event['color']=='warning' ? '#fef3cd' : '#fde8e6'))) }};display:flex;align-items:center;justify-content:center;color:var(--{{ $event['color']=='primary' ? 'primary-light' : ($event['color']=='success' ? 'success' : ($event['color']=='info' ? 'info' : ($event['color']=='warning' ? 'warning' : 'danger'))) }});">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div style="flex:1;">
                    <div class="fw-semi" style="font-size:14px;">{{ is_array($event) ? $event['title'] : $event->title }}</div>
                    <div class="text-muted" style="font-size:12px;"><i class="fas fa-clock" style="margin-right:4px;"></i>{{ is_array($event) ? $event['date'] : $event->event_date }}</div>
                </div>
                <span class="badge badge-{{ is_array($event) ? $event['color'] : 'primary' }}">{{ is_array($event) ? $event['type'] : $event->type }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Second Row -->
<div class="grid-2 mt-3" style="gap:22px;">
    <!-- Recent Counseling -->
    <div class="card animate">
        <div class="card-header">
            <i class="fas fa-heart" style="color:#e87070;"></i>
            <h2>Recent Counseling Requests</h2>
            <div class="card-actions">
                <a href="{{ route('counseling.index') }}" class="btn btn-outline btn-sm">Manage</a>
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Student</th><th>Type</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                    @foreach([
                        ['L','Liza Torres','Academic Stress','Scheduled','May 2'],
                        ['B','Ben Ramos','Personal Issue','Completed','Apr 30'],
                        ['C','Cindy Lim','Career Guidance','Pending','—'],
                        ['M','Mark Vilar','Anxiety','In Progress','May 1'],
                    ] as $c)
                    <tr>
                        <td>
                            <div class="d-flex align-center gap-2">
                                <div class="avatar avatar-sm" style="background:linear-gradient(135deg,#e87070,#c0392b);">{{ $c[0] }}</div>
                                <span class="fw-semi">{{ $c[1] }}</span>
                            </div>
                        </td>
                        <td class="text-muted" style="font-size:13px;">{{ $c[2] }}</td>
                        <td>
                            <span class="badge {{ $c[3]=='Completed' ? 'badge-success' : ($c[3]=='Pending' ? 'badge-warning' : ($c[3]=='Scheduled' ? 'badge-primary' : 'badge-info')) }}">
                                {{ $c[3] }}
                            </span>
                        </td>
                        <td class="text-muted font-mono" style="font-size:12px;">{{ $c[4] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Discipline Cases -->
    <div class="card animate">
        <div class="card-header">
            <i class="fas fa-gavel" style="color:var(--danger);"></i>
            <h2>Active Discipline Cases</h2>
            <div class="card-actions">
                <a href="{{ route('discipline.index') }}" class="btn btn-outline btn-sm">View All</a>
            </div>
        </div>
        <div class="card-body">
            @foreach([
                ['Case #2024-001','Minor Misconduct','Under Investigation','warning'],
                ['Case #2024-002','Academic Dishonesty','Hearing Scheduled','danger'],
                ['Case #2024-003','Property Damage','Pending Documentation','warning'],
                ['Case #2024-004','Dress Code Violation','First Warning Issued','gray'],
            ] as $d)
            <div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid var(--border);">
                <div style="width:38px;height:38px;background:#fde8e6;border-radius:10px;display:flex;align-items:center;justify-content:center;color:var(--danger);font-size:14px;">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div style="flex:1;">
                    <div class="fw-semi" style="font-size:14px;">{{ $d[0] }}</div>
                    <div class="text-muted" style="font-size:12px;">{{ $d[1] }}</div>
                </div>
                <span class="badge badge-{{ $d[3] }}">{{ $d[2] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card mt-3 animate">
    <div class="card-header">
        <i class="fas fa-bolt" style="color:var(--accent);"></i>
        <h2>Quick Actions</h2>
    </div>
    <div class="card-body">
        <div style="display:flex;flex-wrap:wrap;gap:12px;">
            <a href="{{ route('students.create') }}" class="btn btn-primary"><i class="fas fa-user-plus"></i> Enroll Student</a>
            <a href="{{ route('counseling.create') }}" class="btn btn-outline"><i class="fas fa-plus"></i> New Counseling</a>
            <a href="{{ route('discipline.create') }}" class="btn btn-outline"><i class="fas fa-file-plus"></i> File Discipline Case</a>
            <a href="{{ route('events.create') }}" class="btn btn-outline"><i class="fas fa-calendar-plus"></i> Create Event</a>
            <a href="{{ route('scholarships.create') }}" class="btn btn-outline"><i class="fas fa-award"></i> Add Scholarship</a>
            <a href="{{ route('organizations.create') }}" class="btn btn-outline"><i class="fas fa-users"></i> Register Organization</a>
            <a href="{{ route('reports.index') }}" class="btn btn-accent"><i class="fas fa-chart-bar"></i> Generate Report</a>
        </div>
    </div>
</div>
@endsection
