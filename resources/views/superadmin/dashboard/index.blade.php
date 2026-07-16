@extends('superadmin.layouts.app')
@section('title','Dashboard')
@section('page-title','Super Admin Dashboard')
@section('page-sub','System overview — user management and monitoring')
@section('content')
<div class="sg">
<div class="sc an d1"><div class="si w"><i class="fas fa-users"></i></div><div class="sv"><div class="lbl">Total Users</div><div class="val">{{ $stats['total_users'] }}</div><div class="chg">{{ $stats['active_users'] }} active</div></div></div>
<div class="sc an d2"><div class="si g"><i class="fas fa-user-graduate"></i></div><div class="sv"><div class="lbl">Students</div><div class="val">{{ $stats['students'] }}</div><div class="chg">Registered</div></div></div>
<div class="sc an d3"><div class="si y"><i class="fas fa-user-tie"></i></div><div class="sv"><div class="lbl">Staff</div><div class="val">{{ $stats['staff'] }}</div><div class="chg">Admin + Officers</div></div></div>
<div class="sc an d4"><div class="si t"><i class="fas fa-sign-in-alt"></i></div><div class="sv"><div class="lbl">Logins Today</div><div class="val">{{ $stats['online_today'] }}</div><div class="chg">Unique users</div></div></div>
<div class="sc an d5"><div class="si r"><i class="fas fa-times-circle"></i></div><div class="sv"><div class="lbl">Failed Logins</div><div class="val">{{ $stats['failed_logins'] }}</div><div class="chg dn">All time</div></div></div>
<div class="sc an d6"><div class="si o"><i class="fas fa-file-alt"></i></div><div class="sv"><div class="lbl">Applications</div><div class="val">{{ $stats['applications'] }}</div><div class="chg">Scholarship apps</div></div></div>
</div>
<div class="g2 mb3">
<div class="card an"><div class="ch"><i class="fas fa-shield-alt" style="color:var(--y);"></i><h2>Users by Role</h2><div class="ch-acts"><a href="{{ route('superadmin.users.index') }}" class="btn btn-o btn-sm">Manage</a></div></div>
<div class="cb">
@foreach([['superadmin','Super Admin','fas fa-crown','b-sa'],['admin','Admin','fas fa-user-shield','b-y'],['officer','Officer','fas fa-user-tie','b-i'],['student','Student','fas fa-user-graduate','b-g']] as $role)
@php $cnt=$usersByRole[$role[0]]??0; @endphp
<div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid var(--bd);">
<div style="width:40px;height:40px;border-radius:10px;background:rgba(240,192,32,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="{{ $role[2] }}" style="color:var(--y);font-size:15px;"></i></div>
<div style="flex:1;"><div class="fws" style="font-size:13px;color:#fff;">{{ $role[1] }}</div><div style="font-size:11px;color:var(--tm);text-transform:uppercase;letter-spacing:.5px;">{{ $role[0] }}</div></div>
<div style="text-align:right;"><div style="font-family:'Sora',sans-serif;font-size:22px;font-weight:800;color:var(--y);">{{ $cnt }}</div><div style="font-size:11px;color:var(--tm);">users</div></div>
</div>
@endforeach
<div style="margin-top:14px;"><a href="{{ route('superadmin.users.create') }}" class="btn btn-ac" style="width:100%;justify-content:center;"><i class="fas fa-user-plus"></i> Create User & Assign Role</a></div>
</div></div>
<div class="card an"><div class="ch"><i class="fas fa-history" style="color:var(--y);"></i><h2>Recent Login Activity</h2><div class="ch-acts"><a href="{{ route('superadmin.logs') }}" class="btn btn-o btn-sm">View All</a></div></div>
<div class="tw"><table><thead><tr><th>User</th><th>Role</th><th>Status</th><th>Time</th></tr></thead>
<tbody>
@forelse($recentLogins as $log)
<tr>
<td><div style="display:flex;align-items:center;gap:7px;"><div class="av av-s">{{ strtoupper(substr($log->email,0,1)) }}</div><div><div class="fws" style="font-size:12px;color:#fff;">{{ $log->user?->name??'Unknown' }}</div><div class="mono" style="font-size:10px;color:var(--tm);">{{ $log->ip_address }}</div></div></div></td>
<td><span class="badge {{ $log->role==='superadmin'?'b-sa':($log->role==='student'?'b-g':'b-y') }}" style="font-size:10px;">{{ $log->role??'—' }}</span></td>
<td><span class="badge {{ $log->status==='success'?'b-s':($log->status==='failed'?'b-d':'b-w') }}" style="font-size:10px;">{{ ucfirst($log->status) }}</span></td>
<td class="mono" style="font-size:11px;color:var(--tm);">{{ $log->logged_in_at?->diffForHumans() }}</td>
</tr>
@empty
<tr><td colspan="4" style="text-align:center;color:var(--tm);padding:18px;">No login records yet.</td></tr>
@endforelse
</tbody></table></div></div>
</div>
<div class="card an"><div class="ch"><i class="fas fa-bolt" style="color:var(--y);"></i><h2>Quick Actions</h2></div>
<div class="cb" style="display:flex;flex-wrap:wrap;gap:10px;">
<a href="{{ route('superadmin.users.create') }}" class="btn btn-ac"><i class="fas fa-user-plus"></i> Create User & Assign Role</a>
<a href="{{ route('superadmin.users.index') }}" class="btn btn-w"><i class="fas fa-users"></i> Manage Users</a>
<a href="{{ route('superadmin.monitoring') }}" class="btn btn-w"><i class="fas fa-chart-line"></i> Live Monitoring</a>
<a href="{{ route('superadmin.logs') }}" class="btn btn-w"><i class="fas fa-history"></i> Login Logs</a>
<a href="{{ route('superadmin.logs') }}?status=failed" class="btn btn-d btn-sm"><i class="fas fa-exclamation-triangle"></i> Failed Logins</a>
<a href="{{ route('admin.dashboard') }}" class="btn btn-o" target="_blank"><i class="fas fa-external-link-alt"></i> Admin Panel</a>
</div></div>
@endsection
