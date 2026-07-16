@extends('layouts.app')
@section('title', 'Scholarships')
@section('page-title', 'Scholarships & Financial Aid')
@section('page-subtitle', 'Manage scholarship grants and grantees')

@section('content')
<div class="d-flex align-center justify-between mb-3">
    <div class="d-flex gap-2">
        <div class="search-box"><i class="fas fa-search"></i><input type="text" class="form-control" placeholder="Search scholarships..." style="width:240px;"></div>
        <select class="form-control" style="width:150px;"><option>All Types</option><option>Government</option><option>Institutional</option><option>Private</option><option>Merit-based</option></select>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('scholarships.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Scholarship</a>
        <a href="#" class="btn btn-outline"><i class="fas fa-user-plus"></i> Add Grantee</a>
    </div>
</div>

<div class="stats-grid mb-3" style="grid-template-columns:repeat(4,1fr);">
    <div class="stat-card animate delay-1"><div class="stat-icon gold"><i class="fas fa-award"></i></div><div class="stat-info"><div class="label">Total Programs</div><div class="value">18</div></div></div>
    <div class="stat-card animate delay-2"><div class="stat-icon green"><i class="fas fa-user-graduate"></i></div><div class="stat-info"><div class="label">Active Grantees</div><div class="value">892</div></div></div>
    <div class="stat-card animate delay-3"><div class="stat-icon blue"><i class="fas fa-peso-sign"></i></div><div class="stat-info"><div class="label">Total Released (AY)</div><div class="value">₱4.2M</div></div></div>
    <div class="stat-card animate delay-4"><div class="stat-icon teal"><i class="fas fa-file-alt"></i></div><div class="stat-info"><div class="label">Pending Applications</div><div class="value">67</div></div></div>
</div>

<div class="grid-2" style="gap:20px;">
    <div class="card animate">
        <div class="card-header">
            <i class="fas fa-award" style="color:var(--accent);"></i>
            <h2>Scholarship Programs</h2>
            <div class="card-actions"><a href="{{ route('scholarships.create') }}" class="btn btn-accent btn-sm"><i class="fas fa-plus"></i> New</a></div>
        </div>
        <div class="card-body">
            @foreach([
                ['CHED Scholarship','Government','250','Full Tuition','Merit + Financial Need','Active'],
                ['TES (Tertiary Edu. Subsidy)','Government','180','₱40,000/year','Financial Need','Active'],
                ['DOST-SEI Scholarship','Government','45','Full + Stipend','Science & Math','Active'],
                ['Presidential Scholarship','Institutional','30','Full Tuition','Academic Excellence','Active'],
                ['Athletic Scholarship','Institutional','60','50% Tuition','Sports Performance','Active'],
                ['SM College Scholarship','Private','25','Full Tuition','Merit + Leadership','Active'],
                ['Gokongwei Foundation','Private','20','₱50,000/year','Engineering Students','Active'],
                ['Cultural Arts Grant','Institutional','22','25% Tuition','Cultural Performance','Active'],
            ] as $sch)
            <div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid var(--border);">
                <div style="width:42px;height:42px;background:#fef6e0;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#c8972b;font-size:16px;">
                    <i class="fas fa-award"></i>
                </div>
                <div style="flex:1;">
                    <div class="fw-semi" style="font-size:14px;">{{ $sch[0] }}</div>
                    <div class="text-muted" style="font-size:12px;">{{ $sch[2] }} grantees · {{ $sch[3] }}</div>
                </div>
                <div style="text-align:right;">
                    <span class="badge {{ $sch[1]=='Government' ? 'badge-primary' : ($sch[1]=='Private' ? 'badge-success' : 'badge-info') }}">{{ $sch[1] }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="card animate">
        <div class="card-header">
            <i class="fas fa-users" style="color:var(--primary-light);"></i>
            <h2>Recent Grantees</h2>
            <div class="card-actions"><a href="#" class="btn btn-outline btn-sm">View All</a></div>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Student</th><th>Scholarship</th><th>GWA</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach([
                        ['M','Maria Santos','CHED Scholarship','1.50','Active'],
                        ['J','Juan Dela Cruz','TES','1.75','Active'],
                        ['A','Ana Reyes','DOST-SEI','1.25','Active'],
                        ['C','Carlos Mendoza','Presidential','1.40','Active'],
                        ['R','Rosa Garcia','Athletic','1.80','Active'],
                        ['P','Pedro Lim','SM College','1.60','Active'],
                        ['E','Elena Cruz','Gokongwei','1.35','Active'],
                        ['T','Tony Bautista','Cultural Arts','2.00','Probation'],
                    ] as $g)
                    <tr>
                        <td>
                            <div class="d-flex align-center gap-2">
                                <div class="avatar avatar-sm">{{ $g[0] }}</div>
                                <span class="fw-semi">{{ $g[1] }}</span>
                            </div>
                        </td>
                        <td class="text-muted" style="font-size:12px;">{{ $g[2] }}</td>
                        <td><span class="fw-semi {{ $g[3] <= 1.5 ? 'text-success' : 'text-primary' }}">{{ $g[3] }}</span></td>
                        <td><span class="badge {{ $g[4]=='Active' ? 'badge-success' : 'badge-warning' }}">{{ $g[4] }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
