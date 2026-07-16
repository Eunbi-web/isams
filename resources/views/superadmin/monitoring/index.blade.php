@extends('superadmin.layouts.app')
@section('title','Live Monitoring')
@section('page-title','Live System Monitoring')
@section('page-sub','Real-time overview of system activity')
@section('content')
<div class="alert al-y an" style="margin-bottom:16px;"><i class="fas fa-circle" style="color:var(--gm);font-size:10px;"></i><span>Live monitoring active — <a href="#" onclick="location.reload()" style="color:var(--y);font-weight:700;">Refresh Now</a></span></div>
<div class="sg" style="grid-template-columns:repeat(4,1fr);">
<div class="sc an d1"><div class="si g"><i class="fas fa-users"></i></div><div class="sv"><div class="lbl">Online Today</div><div class="val">{{ $stats['users_online_today'] }}</div><div class="chg">Unique users</div></div></div>
<div class="sc an d2"><div class="si y"><i class="fas fa-sign-in-alt"></i></div><div class="sv"><div class="lbl">Logins Today</div><div class="val">{{ $stats['login_today'] }}</div><div class="chg">All attempts</div></div></div>
<div class="sc an d3"><div class="si r"><i class="fas fa-exclamation-triangle"></i></div><div class="sv"><div class="lbl">Failed Today</div><div class="val">{{ $stats['failed_logins_today'] }}</div><div class="chg dn">Today</div></div></div>
<div class="sc an d4"><div class="si o"><i class="fas fa-file-alt"></i></div><div class="sv"><div class="lbl">Applications</div><div class="val">{{ $stats['total_applications'] }}</div><div class="chg">Total</div></div></div>
</div>
<div class="sg" style="grid-template-columns:repeat(4,1fr);margin-bottom:20px;">
<div class="sc an"><div class="si g"><i class="fas fa-check-circle"></i></div><div class="sv"><div class="lbl">AI Eligible</div><div class="val">{{ $stats['eligible_count'] }}</div></div></div>
<div class="sc an"><div class="si y"><i class="fas fa-award"></i></div><div class="sv"><div class="lbl">Active Scholarships</div><div class="val">{{ $stats['scholarships_active'] }}</div></div></div>
<div class="sc an"><div class="si t"><i class="fas fa-stream"></i></div><div class="sv"><div class="lbl">Counseling Queue</div><div class="val">{{ $stats['counseling_queue'] }}</div></div></div>
<div class="sc an"><div class="si w"><i class="fas fa-user-graduate"></i></div><div class="sv"><div class="lbl">Total Students</div><div class="val">{{ $stats['total_students'] }}</div></div></div>
</div>
<div class="g2 mb3">
<div class="card an"><div class="ch"><i class="fas fa-chart-bar" style="color:var(--y);"></i><h2>Login Activity Today (by Hour)</h2></div>
<div class="cb">
@php $maxVal=max(1,max($logins_by_hour->values()->toArray()+[0])); @endphp
<div style="display:flex;align-items:flex-end;gap:3px;height:100px;">
@for($h=6;$h<=22;$h++)
@php $cnt=$logins_by_hour[$h]??0; $pct=$maxVal>0?round(($cnt/$maxVal)*100):0; @endphp
<div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:3px;">
<div style="font-size:9px;color:var(--tm);">{{ $cnt>0?$cnt:'' }}</div>
<div style="width:100%;background:{{ $pct>0?'linear-gradient(to top,var(--y),var(--gm))':'var(--bd)' }};border-radius:3px 3px 0 0;height:{{ max(4,$pct) }}%;"></div>
<div style="font-size:8px;color:var(--tm);">{{ $h%12?:12 }}{{ $h>=12?'p':'a' }}</div>
</div>
@endfor
</div>
<div style="display:flex;justify-content:space-between;margin-top:8px;font-size:11px;color:var(--tm);">
<span>Total today: {{ $logins_by_hour->sum() }} logins</span>
</div>
</div></div>
<div class="card an"><div class="ch"><i class="fas fa-stream" style="color:var(--y);"></i><h2>Live Activity Feed</h2><div class="ch-acts"><div style="font-size:11px;color:var(--gm);"><span class="online-dot"></span>Live</div></div></div>
<div style="max-height:220px;overflow-y:auto;">
@forelse($recent_activity as $act)
<div style="display:flex;align-items:center;gap:10px;padding:10px 18px;border-bottom:1px solid var(--bd);">
<div style="width:8px;height:8px;border-radius:50%;background:{{ $act->status==='success'?'var(--gm)':($act->status==='failed'?'var(--danger)':'var(--warn)') }};flex-shrink:0;"></div>
<div style="flex:1;min-width:0;"><div class="fws" style="font-size:12px;color:#fff;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $act->user?->name??$act->email }}</div><div style="font-size:11px;color:var(--tm);">{{ ucfirst($act->status) }} · {{ $act->role??'Unknown' }} · {{ $act->ip_address }}</div></div>
<div style="font-size:11px;color:var(--tm);white-space:nowrap;">{{ $act->logged_in_at?->diffForHumans() }}</div>
</div>
@empty
<div style="padding:20px;text-align:center;color:var(--tm);">No recent activity.</div>
@endforelse
</div></div>
</div>
<div class="card an"><div class="ch"><i class="fas fa-database" style="color:var(--y);"></i><h2>System Data Overview</h2></div>
<div class="tw"><table>
<thead><tr><th>Module</th><th>Total</th><th>Active</th><th>Today</th><th>Health</th></tr></thead>
<tbody>
@foreach([['Users',\App\Models\User::count(),\App\Models\User::where('is_active',true)->count(),\App\Models\User::whereDate('created_at',today())->count()],['Students',\App\Models\Student::count(),\App\Models\Student::where('status','Active')->count(),\App\Models\Student::whereDate('created_at',today())->count()],['Scholarships',\App\Models\Scholarship::count(),\App\Models\Scholarship::where('status','Active')->count(),0],['Applications',\App\Models\ScholarshipApplication::count(),\App\Models\ScholarshipApplication::where('status','Approved')->count(),\App\Models\ScholarshipApplication::whereDate('created_at',today())->count()],['Counseling',\App\Models\CounselingSession::count(),\App\Models\CounselingSession::where('status','In Queue')->count(),\App\Models\CounselingSession::whereDate('created_at',today())->count()],['Login Logs',\App\Models\LoginLog::count(),\App\Models\LoginLog::whereNull('logged_out_at')->where('status','success')->count(),\App\Models\LoginLog::whereDate('logged_in_at',today())->count()]] as $row)
<tr>
<td class="fws" style="color:#fff;">{{ $row[0] }}</td>
<td style="font-family:'JetBrains Mono',monospace;font-weight:700;color:var(--y);">{{ number_format($row[1]) }}</td>
<td style="font-family:'JetBrains Mono',monospace;color:var(--gm);">{{ number_format($row[2]) }}</td>
<td style="font-family:'JetBrains Mono',monospace;color:var(--tm);">+{{ number_format($row[3]) }}</td>
<td><span class="badge b-s" style="font-size:10px;"><i class="fas fa-check" style="margin-right:3px;"></i>Healthy</span></td>
</tr>
@endforeach
</tbody></table></div></div>
@push('scripts')<script>setTimeout(()=>location.reload(),30000);</script>@endpush
@endsection
