@extends('layouts.app')
@section('title', 'Reports')
@section('page-title', 'Reports & Analytics')
@section('page-subtitle', 'Generate and view comprehensive reports')

@section('content')
<div class="grid-2 mb-3" style="gap:20px;">
    <!-- Enrollment by Course -->
    <div class="card animate">
        <div class="card-header">
            <i class="fas fa-chart-pie" style="color:var(--primary-light);"></i>
            <h2>Enrollment by Course</h2>
            <div class="card-actions"><a href="#" class="btn btn-outline btn-sm"><i class="fas fa-download"></i> Export</a></div>
        </div>
        <div class="card-body">
            @foreach([
                ['BS Computer Science',680,'blue',680/4280*100],
                ['BS Nursing',540,'teal',540/4280*100],
                ['BS Education',520,'green',520/4280*100],
                ['BS Engineering',490,'gold',490/4280*100],
                ['BS Business Admin',460,'purple',460/4280*100],
                ['Others',1590,'gray',1590/4280*100],
            ] as $c)
            <div style="margin-bottom:14px;">
                <div style="display:flex;justify-content:space-between;margin-bottom:5px;">
                    <span style="font-size:13px;font-weight:600;">{{ $c[0] }}</span>
                    <span style="font-size:12px;color:var(--text-muted);">{{ $c[1] }} ({{ number_format($c[3], 1) }}%)</span>
                </div>
                <div style="height:8px;background:#eef1f5;border-radius:10px;overflow:hidden;">
                    <div style="height:100%;width:{{ $c[3] }}%;background:{{ $c[1]=='blue'?'var(--primary-light)':($c[1]=='teal'?'var(--info)':($c[1]=='green'?'var(--success)':($c[1]=='gold'?'var(--accent)':($c[1]=='purple'?'#7b4fcf':'#b0c0d0')))) }};border-radius:10px;"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Counseling Stats -->
    <div class="card animate">
        <div class="card-header">
            <i class="fas fa-chart-bar" style="color:#e87070;"></i>
            <h2>Counseling (Monthly)</h2>
            <div class="card-actions"><a href="#" class="btn btn-outline btn-sm"><i class="fas fa-download"></i> Export</a></div>
        </div>
        <div class="card-body">
            @foreach([
                ['Academic Stress',45,'danger'],
                ['Personal Issues',32,'warning'],
                ['Career Guidance',28,'primary'],
                ['Mental Health',24,'info'],
                ['Family Concerns',18,'success'],
                ['Financial',9,'gray'],
            ] as $cs)
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                <div style="width:130px;font-size:13px;font-weight:500;">{{ $cs[0] }}</div>
                <div style="flex:1;height:24px;background:#eef1f5;border-radius:6px;overflow:hidden;position:relative;">
                    <div style="height:100%;width:{{ ($cs[1]/45)*100 }}%;background:{{ $cs[2]=='danger'?'#e87070':($cs[2]=='warning'?'#f0b429':($cs[2]=='primary'?'var(--primary-light)':($cs[2]=='info'?'var(--info)':($cs[2]=='success'?'var(--success)':'#b0c0d0')))) }};border-radius:6px;display:flex;align-items:center;justify-content:flex-end;padding-right:8px;">
                        <span style="font-size:11px;font-weight:700;color:white;">{{ $cs[1] }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Quick Reports -->
<div class="card animate mb-3">
    <div class="card-header">
        <i class="fas fa-file-alt" style="color:var(--accent);"></i>
        <h2>Generate Reports</h2>
    </div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:14px;">
            @foreach([
                ['Student Masterlist','fas fa-users','Generate full student list with details','primary'],
                ['Enrollment Summary','fas fa-chart-line','Enrollment stats by course/year/sex','blue'],
                ['Scholar Listing','fas fa-award','All active scholarship grantees','gold'],
                ['Counseling Report','fas fa-heart','Sessions summary and case types','red'],
                ['Discipline Report','fas fa-gavel','Cases filed, hearings, resolutions','danger'],
                ['Organizations Report','fas fa-users-cog','Org list with member count','teal'],
                ['Events Summary','fas fa-calendar','Events conducted and participation','purple'],
                ['Clearance Report','fas fa-check-double','Student clearance status','green'],
            ] as $r)
            <div class="card" style="cursor:pointer;transition:all .2s;" onmouseover="this.style.borderColor='var(--primary-light)';this.style.transform='translateY(-2px)';" onmouseout="this.style.borderColor='var(--border)';this.style.transform='';">
                <div class="card-body" style="padding:16px;text-align:center;">
                    <div class="stat-icon {{ $r[3] }}" style="margin:0 auto 10px;border-radius:12px;"><i class="{{ $r[1] }}"></i></div>
                    <div class="fw-semi" style="font-size:13px;margin-bottom:4px;">{{ $r[0] }}</div>
                    <div class="text-muted" style="font-size:11px;margin-bottom:12px;">{{ $r[2] }}</div>
                    <div style="display:flex;gap:6px;justify-content:center;">
                        <a href="#" class="btn btn-outline btn-sm" style="font-size:11px;"><i class="fas fa-eye"></i> View</a>
                        <a href="#" class="btn btn-primary btn-sm" style="font-size:11px;"><i class="fas fa-download"></i> PDF</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Key Metrics Table -->
<div class="card animate">
    <div class="card-header">
        <i class="fas fa-table" style="color:var(--primary-light);"></i>
        <h2>Summary Statistics (AY 2023–2024)</h2>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Module</th><th>Total Records</th><th>Active</th><th>This Month</th><th>Growth</th></tr></thead>
            <tbody>
                @foreach([
                    ['Students','4,280','4,142','124','+3.0%','success'],
                    ['Organizations','38','34','2','+5.6%','success'],
                    ['Scholars','892','856','45','+5.3%','success'],
                    ['Counseling Sessions','1,247','156','89','+12.0%','success'],
                    ['Discipline Cases','47','14','6','-18.0%','danger'],
                    ['Events','84','22','5','+29.0%','success'],
                ] as $m)
                <tr>
                    <td class="fw-semi">{{ $m[0] }}</td>
                    <td class="fw-semi text-primary">{{ $m[1] }}</td>
                    <td>{{ $m[2] }}</td>
                    <td>{{ $m[3] }}</td>
                    <td><span class="badge {{ $m[5]=='success' ? 'badge-success' : 'badge-danger' }}">{{ $m[4] }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
