@extends('superadmin.layouts.app')
@section('title','User Management')
@section('page-title','User Management')
@section('page-sub','Create accounts and assign roles — all role assignments done by Super Admin only')
@section('content')
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:18px;">
<div style="display:flex;gap:9px;flex-wrap:wrap;">
<div style="position:relative;"><i class="fas fa-search" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--tm);font-size:12px;pointer-events:none;"></i><input type="text" id="uSearch" class="fc" placeholder="Search name or email..." style="padding-left:32px;width:220px;"></div>
<select class="fc" style="width:150px;" id="rFilter"><option value="">All Roles</option><option value="superadmin">Super Admin</option><option value="admin">Admin</option><option value="officer">Officer</option><option value="student">Student</option></select>
</div>
<a href="{{ route('superadmin.users.create') }}" class="btn btn-ac"><i class="fas fa-user-plus"></i> Create User & Assign Role</a>
</div>
<div class="card an"><div class="ch"><i class="fas fa-users" style="color:var(--y);"></i><h2>All System Users</h2><span class="badge b-y" style="margin-left:6px;">{{ $users->total() }} total</span></div>
<div class="tw"><table id="uTable">
<thead><tr><th>User</th><th>Role</th><th>Department</th><th>Status</th><th>Last Login</th><th>Actions</th></tr></thead>
<tbody>
@forelse($users as $user)
<tr data-role="{{ $user->role }}">
<td><div style="display:flex;align-items:center;gap:9px;"><div class="av" style="width:34px;height:34px;font-size:13px;background:{{ $user->role==='superadmin'?'linear-gradient(135deg,var(--y),var(--yd))':'rgba(240,192,32,.15)' }};color:{{ $user->role==='superadmin'?'#0d3318':'var(--y)' }};">{{ strtoupper(substr($user->name,0,1)) }}</div><div><div class="fws" style="font-size:13px;color:#fff;">{{ $user->name }}</div><div class="mono" style="font-size:11px;color:var(--tm);">{{ $user->email }}</div></div></div></td>
<td><span class="badge {{ $user->role==='superadmin'?'b-sa':($user->role==='student'?'b-g':($user->role==='admin'?'b-y':'b-i')) }}">@if($user->role==='superadmin')<i class="fas fa-crown" style="margin-right:4px;font-size:9px;"></i>@endif{{ ucfirst($user->role) }}</span></td>
<td style="font-size:12px;color:var(--tm);">{{ $user->department??'—' }}</td>
<td><span class="badge {{ $user->is_active?'b-s':'b-d' }}">{{ $user->is_active?'Active':'Inactive' }}</span></td>
<td class="mono" style="font-size:11px;color:var(--tm);">{{ $user->last_login_at?$user->last_login_at->diffForHumans():'Never' }}</td>
<td><div style="display:flex;gap:5px;">
<a href="{{ route('superadmin.users.edit',$user->id) }}" class="btn btn-o btn-sm btn-ic" title="Edit"><i class="fas fa-edit"></i></a>
@if($user->role!=='superadmin')
<form method="POST" action="{{ route('superadmin.users.toggle',$user->id) }}">@csrf @method('PATCH')
<button class="btn {{ $user->is_active?'btn-d':'btn-s' }} btn-sm btn-ic" onclick="return confirm('{{ $user->is_active?'Deactivate':'Activate' }} this user?')"><i class="fas fa-{{ $user->is_active?'ban':'check' }}"></i></button>
</form>
@endif
</div></td>
</tr>
@empty
<tr><td colspan="6" style="text-align:center;padding:18px;color:var(--tm);">No users found. <a href="{{ route('superadmin.users.create') }}" style="color:var(--y);">Create one</a></td></tr>
@endforelse
</tbody></table></div>
@if($users->hasPages())<div style="padding:13px 18px;border-top:1px solid var(--bd);">{{ $users->links() }}</div>@endif
</div>
@push('scripts')
<script>
document.getElementById('uSearch').addEventListener('input',function(){const q=this.value.toLowerCase();document.querySelectorAll('#uTable tbody tr').forEach(r=>r.style.display=r.textContent.toLowerCase().includes(q)?'':'none');});
document.getElementById('rFilter').addEventListener('change',function(){const v=this.value;document.querySelectorAll('#uTable tbody tr[data-role]').forEach(r=>r.style.display=(!v||r.dataset.role===v)?'':'none');});
</script>
@endpush
@endsection
