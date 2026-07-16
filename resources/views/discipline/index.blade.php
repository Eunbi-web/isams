@extends('layouts.app')
@section('title', 'Discipline')
@section('page-title', 'Discipline Office')
@section('page-subtitle', 'Manage student discipline cases and proceedings')

@section('content')
<div class="d-flex align-center justify-between mb-3">
    <div class="d-flex gap-2">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" class="form-control" placeholder="Search cases..." style="width:240px;">
        </div>
        <select class="form-control" style="width:160px;">
            <option value="">All Violations</option>
            <option>Academic Dishonesty</option>
            <option>Property Damage</option>
            <option>Misconduct</option>
            <option>Dress Code</option>
            <option>Substance Abuse</option>
            <option>Harassment</option>
        </select>
        <select class="form-control" style="width:150px;">
            <option value="">All Status</option>
            <option>Under Investigation</option>
            <option>Hearing Scheduled</option>
            <option>Pending Documentation</option>
            <option>Resolved</option>
            <option>Dismissed</option>
        </select>
    </div>
    <a href="{{ route('discipline.create') }}" class="btn btn-primary">
        <i class="fas fa-file-plus"></i> File Case
    </a>
</div>

<div class="stats-grid mb-3" style="grid-template-columns:repeat(5,1fr);">
    <div class="stat-card animate delay-1">
        <div class="stat-icon red"><i class="fas fa-file-alt"></i></div>
        <div class="stat-info"><div class="label">Total Cases (AY)</div><div class="value">47</div></div>
    </div>
    <div class="stat-card animate delay-2">
        <div class="stat-icon warning"><i class="fas fa-search"></i></div>
        <div class="stat-info"><div class="label">Under Investigation</div><div class="value">8</div></div>
    </div>
    <div class="stat-card animate delay-3">
        <div class="stat-icon blue"><i class="fas fa-calendar"></i></div>
        <div class="stat-info"><div class="label">Hearings Sched.</div><div class="value">6</div></div>
    </div>
    <div class="stat-card animate delay-4">
        <div class="stat-icon green"><i class="fas fa-check"></i></div>
        <div class="stat-info"><div class="label">Resolved</div><div class="value">29</div></div>
    </div>
    <div class="stat-card animate delay-5">
        <div class="stat-icon teal"><i class="fas fa-ban"></i></div>
        <div class="stat-info"><div class="label">Dismissed</div><div class="value">4</div></div>
    </div>
</div>

<div class="card animate">
    <div class="card-header">
        <i class="fas fa-gavel" style="color:var(--danger);"></i>
        <h2>Discipline Cases</h2>
        <span class="badge badge-danger" style="margin-left:6px;">14 Active</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Case No.</th>
                    <th>Student</th>
                    <th>Violation</th>
                    <th>Date Filed</th>
                    <th>Hearing Date</th>
                    <th>Status</th>
                    <th>Sanction</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach([
                    ['2024-DC-001','Maria Santos','Academic Dishonesty','Apr 10, 2024','May 5, 2024','Hearing Scheduled','—','danger'],
                    ['2024-DC-002','Juan Dela Cruz','Minor Misconduct','Apr 12, 2024','—','Under Investigation','—','warning'],
                    ['2024-DC-003','Carlos Mendoza','Property Damage','Apr 15, 2024','May 8, 2024','Hearing Scheduled','—','danger'],
                    ['2024-DC-004','Rosa Garcia','Dress Code Violation','Apr 18, 2024','—','First Warning Issued','Written Warning','gray'],
                    ['2024-DC-005','Pedro Lim','Substance Abuse (Suspected)','Apr 20, 2024','May 10, 2024','Under Investigation','—','danger'],
                    ['2024-DC-006','Ana Reyes','Tardiness (Repeated)','Mar 5, 2024','—','Resolved','Probation','success'],
                    ['2024-DC-007','Tony Bautista','Unauthorized Use of Facilities','Apr 22, 2024','—','Pending Documentation','—','warning'],
                    ['2024-DC-008','Cindy Lim','Harassment Complaint','Apr 1, 2024','Apr 28, 2024','Resolved','Suspension (3 days)','success'],
                ] as $d)
                <tr>
                    <td class="font-mono" style="font-size:12px;color:var(--text-muted);">{{ $d[0] }}</td>
                    <td>
                        <div class="d-flex align-center gap-2">
                            <div class="avatar avatar-sm" style="background:linear-gradient(135deg,#c0392b,#922b21);">{{ substr($d[1],0,1) }}</div>
                            <span class="fw-semi">{{ $d[1] }}</span>
                        </div>
                    </td>
                    <td style="font-size:13px;max-width:180px;">{{ $d[2] }}</td>
                    <td class="text-muted font-mono" style="font-size:12px;">{{ $d[3] }}</td>
                    <td class="text-muted font-mono" style="font-size:12px;">{{ $d[4] }}</td>
                    <td>
                        <span class="badge {{ $d[7]=='danger' ? 'badge-danger' : ($d[7]=='warning' ? 'badge-warning' : ($d[7]=='success' ? 'badge-success' : 'badge-gray')) }}">
                            {{ $d[5] }}
                        </span>
                    </td>
                    <td class="text-muted" style="font-size:13px;">{{ $d[6] }}</td>
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
