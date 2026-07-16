@extends('admin.layouts.app')
@section('title','Scholarship')
@section('page-title','{{ $scholarship->name }}')
@section('content')
<div style="max-width:760px;">
<div style="margin-bottom:14px;display:flex;gap:8px;"><a href="{{ route('admin.scholarships.index') }}" class="btn btn-o btn-sm"><i class="fas fa-arrow-left"></i> Back</a><a href="{{ route('admin.scholarships.edit',$scholarship->id) }}" class="btn btn-p btn-sm"><i class="fas fa-edit"></i> Edit</a></div>
<div class="card an"><div class="ch"><div class="si y" style="width:40px;height:40px;font-size:16px;border-radius:10px;flex-shrink:0;"><i class="fas fa-award"></i></div><div><h2>{{ $scholarship->name }}</h2><div class="tm" style="font-size:12px;">{{ $scholarship->type }}</div></div><div class="ch-acts"><span class="badge {{ $scholarship->status==='Active'?'b-s':'b-d' }}">{{ $scholarship->status }}</span></div></div>
<div class="cb">
<div class="g2 mb3">
@foreach([['Benefit',$scholarship->benefits??'—','fas fa-gift'],['Slots',$scholarship->slots??'—','fas fa-users'],['Source',$scholarship->source??'—','fas fa-university'],['Deadline',$scholarship->end_date?->format('M d, Y')??'—','fas fa-calendar']] as $d)
<div style="background:var(--bg);border:1px solid var(--bd);border-radius:var(--rs);padding:12px;"><div style="font-size:11px;color:var(--tm);margin-bottom:3px;"><i class="{{ $d[2] }}" style="margin-right:4px;"></i>{{ $d[0] }}</div><div class="fws" style="font-size:14px;">{{ $d[1] }}</div></div>
@endforeach
</div>
@if($scholarship->requirements)<div class="alert al-i" style="margin-bottom:14px;font-size:13px;"><i class="fas fa-list"></i><span>{{ $scholarship->requirements }}</span></div>@endif
@if($scholarship->ai_criteria)<div class="alert al-ai" style="margin-bottom:0;font-size:12px;"><i class="fas fa-robot"></i><span><strong>AI Criteria:</strong> {{ json_encode($scholarship->ai_criteria) }}</span></div>@endif
</div></div>
<div class="card an mt3"><div class="ch"><i class="fas fa-file-alt" style="color:var(--gm);"></i><h2>Applications ({{ $scholarship->applications->count() }})</h2></div>
<div class="tw"><table><thead><tr><th>Student</th><th>GWA</th><th>AI Score</th><th>Eligibility</th><th>Status</th></tr></thead>
<tbody>
@forelse($scholarship->applications as $app)
@php $sc=$app->ai_score??0; $el=$app->ai_eligibility==='Eligible'?'el':($app->ai_eligibility==='For Review'?'rv':'no'); @endphp
<tr>
<td><div style="display:flex;align-items:center;gap:7px;"><div class="av av-s">{{ strtoupper(substr($app->student->first_name??'S',0,1)) }}</div><span class="fws" style="font-size:13px;">{{ $app->student->full_name??'—' }}</span></div></td>
<td class="mono fwb" style="color:var(--gm);">{{ number_format($app->gwa??0,2) }}</td>
<td><div style="display:flex;align-items:center;gap:5px;"><div style="flex:1;"><div class="asb"><div class="asf {{ $sc>=75?'ash':($sc>=50?'asm':'asl') }}" style="width:{{ $sc }}%;"></div></div></div><span class="mono" style="font-size:11px;font-weight:700;">{{ $sc }}%</span></div></td>
<td><span class="badge elig-{{ $el }}" style="font-size:10px;">{{ $app->ai_eligibility??'—' }}</span></td>
<td><span class="badge {{ $app->status==='Approved'?'b-s':($app->status==='Rejected'?'b-d':'b-w') }}">{{ $app->status }}</span></td>
</tr>
@empty
<tr><td colspan="5" style="text-align:center;padding:16px;color:var(--tm);">No applications yet.</td></tr>
@endforelse
</tbody></table></div></div>
</div>
@endsection
