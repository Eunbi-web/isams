@extends('superadmin.layouts.app')
@section('title','Login Logs')
@section('page-title','Login Logs')
@section('page-sub','All system login activity and audit trail')
@section('content')
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
<div style="display:flex;gap:9px;flex-wrap:wrap;">
<div style="position:relative;"><i class="fas fa-search" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--tm);font-size:12px;pointer-events:none;"></i><input type="text" id="lSearch" class="fc" placeholder="Search email or IP..." style="padding-left:32px;width:200px;"></div>
<form method="GET" action="{{ route('superadmin.logs') }}" style="display:flex;gap:8px;">
<select name="status" class="fc" style="width:130px;" onchange="this.form.submit()"><option value="">All Status</option><option value="success" {{ request('status')==='success'?'selected':'' }}>Success</option><option value="failed" {{ request('status')==='failed'?'selected':'' }}>Failed</option><option value="blocked" {{ request('status')==='blocked'?'selected':'' }}>Blocked</option></select>
<select name="role" class="fc" style="width:130px;" onchange="this.form.submit()"><option value="">All Roles</option><option value="superadmin" {{ request('role')==='superadmin'?'selected':'' }}>Super Admin</option><option value="admin" {{ request('role')==='admin'?'selected':'' }}>Admin</option><option value="officer" {{ request('role')==='officer'?'selected':'' }}>Officer</option><option value="student" {{ request('role')==='student'?'selected':'' }}>Student</option></select>
</form>
</div>
<div style="font-size:13px;color:var(--tm);">Total: <strong style="color:#fff;">{{ $logs->total() }}</strong></div>
</div>
<div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px;">
@foreach([["Today's Logins",\App\Models\LoginLog::whereDate('logged_in_at',today())->count(),'y'],['Successful',\App\Models\LoginLog::where('status','success')->count(),'g'],['Failed',\App\Models\LoginLog::where('status','failed')->count(),'r'],['Blocked',\App\Models\LoginLog::where('status','blocked')->count(),'o']] as $chip)
<div style="background:var(--card);border:1px solid var(--bd);border-radius:20px;padding:7px 16px;display:flex;align-items:center;gap:8px;">
<span style="font-family:'Sora',sans-serif;font-size:18px;font-weight:800;color:{{ $chip[2]==='y'?'var(--y)':($chip[2]==='g'?'var(--gm)':($chip[2]==='r'?'var(--danger)':'var(--warn)')) }};">{{ $chip[1] }}</span>
<span style="font-size:12px;color:var(--tm);">{{ $chip[0] }}</span>
</div>
@endforeach
</div>
<div class="card an"><div class="ch"><i class="fas fa-history" style="color:var(--y);"></i><h2>Login Activity Log</h2></div>
<div class="tw"><table id="lTable">
<thead><tr><th>#</th><th>User</th><th>Role</th><th>IP Address</th><th>Status</th><th>Login Time</th><th>Logout</th></tr></thead>
<tbody>
@forelse($logs as $log)
<tr>
<td class="mono" style="font-size:11px;color:var(--tm);">{{ $log->id }}</td>
<td><div><div class="fws" style="font-size:12px;color:#fff;">{{ $log->user?->name??'Unknown' }}</div><div class="mono" style="font-size:10px;color:var(--tm);">{{ $log->email }}</div></div></td>
<td>@if($log->role)<span class="badge {{ $log->role==='superadmin'?'b-sa':($log->role==='student'?'b-g':($log->role==='admin'?'b-y':'b-i')) }}" style="font-size:10px;">{{ ucfirst($log->role) }}</span>@else<span class="badge b-gray" style="font-size:10px;">—</span>@endif</td>
<td class="mono" style="font-size:12px;color:var(--tm);">{{ $log->ip_address??'—' }}</td>
<td><span class="badge {{ $log->status==='success'?'b-s':($log->status==='failed'?'b-d':'b-w') }}" style="font-size:10px;"><i class="fas fa-{{ $log->status==='success'?'check':'times' }}" style="font-size:9px;margin-right:3px;"></i>{{ ucfirst($log->status) }}</span></td>
<td class="mono" style="font-size:11px;">{{ $log->logged_in_at?->format('M d, Y h:i A')??'—' }}</td>
<td class="mono" style="font-size:11px;color:var(--tm);">{{ $log->logged_out_at?->format('h:i A')??'—' }}</td>
</tr>
@empty
<tr><td colspan="7" style="text-align:center;padding:20px;color:var(--tm);">No login logs yet.</td></tr>
@endforelse
</tbody></table></div>
@if($logs->hasPages())<div style="padding:13px 18px;border-top:1px solid var(--bd);">{{ $logs->links() }}</div>@endif
</div>
@push('scripts')
<script>document.getElementById('lSearch').addEventListener('input',function(){const q=this.value.toLowerCase();document.querySelectorAll('#lTable tbody tr').forEach(r=>r.style.display=r.textContent.toLowerCase().includes(q)?'':'none');});</script>
@endpush
@endsection
