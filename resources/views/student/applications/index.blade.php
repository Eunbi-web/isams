@extends('student.layouts.app')
@section('title','My Applications')
@section('page-title','My Applications')
@section('page-sub','Track the status of all your scholarship applications')
@section('content')
<div style="display:flex;justify-content:flex-end;margin-bottom:16px;">
<a href="{{ route('student.scholarships') }}" class="btn btn-p btn-sm"><i class="fas fa-plus"></i> Apply to Scholarship</a>
</div>
<div class="card an">
<div class="ch"><i class="fas fa-file-alt" style="color:var(--gm);"></i><h2>All Applications</h2><span class="badge b-p" style="margin-left:6px;">{{ $applications->count() }}</span></div>
<div class="tw">
<table><thead><tr><th>Scholarship</th><th>GWA</th><th>AI Score</th><th>Eligibility</th><th>Status</th><th>Date Filed</th><th>Action</th></tr></thead>
<tbody>
@forelse($applications as $app)
@php $sc=$app->ai_score??0; $el=$app->ai_eligibility==='Eligible'?'el':($app->ai_eligibility==='For Review'?'rv':'no'); @endphp
<tr>
<td><div class="fws" style="font-size:13px;">{{ $app->scholarship->name??'—' }}</div><div class="tm mono" style="font-size:11px;">{{ $app->scholarship->type??'' }}</div></td>
<td class="mono fwb" style="color:{{ (float)$app->gwa<=1.75?'var(--gm)':((float)$app->gwa<=2.25?'var(--warn)':'var(--danger)') }}">{{ number_format($app->gwa,2) }}</td>
<td style="min-width:100px;"><div style="display:flex;align-items:center;gap:5px;"><div style="flex:1;"><div class="asb"><div class="asf {{ $sc>=75?'ash':($sc>=50?'asm':'asl') }}" style="width:{{ $sc }}%;"></div></div></div><span class="mono" style="font-size:11px;font-weight:700;">{{ $sc }}%</span></div></td>
<td><span class="badge elig-{{ $el }}" style="font-size:10px;">{{ $app->ai_eligibility??'Pending' }}</span></td>
<td><span class="badge {{ $app->status==='Approved'?'b-s':($app->status==='Rejected'?'b-d':'b-w') }}">{{ $app->status }}</span></td>
<td class="mono" style="font-size:11px;color:var(--tm);">{{ $app->created_at->format('M d, Y') }}</td>
<td><a href="{{ route('student.applications.show',$app->id) }}" class="btn btn-o btn-sm btn-ic"><i class="fas fa-eye"></i></a></td>
</tr>
@empty
<tr><td colspan="7" style="text-align:center;padding:24px;color:var(--tm);">No applications yet. <a href="{{ route('student.scholarships') }}" style="color:var(--gm);">Browse scholarships</a></td></tr>
@endforelse
</tbody></table>
</div></div>
@endsection
