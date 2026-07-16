@extends('admin.layouts.app')
@section('title','Student')
@section('page-title','Student Profile')
@section('content')
<div style="max-width:760px;">
<div style="margin-bottom:14px;display:flex;gap:8px;"><a href="{{ route('admin.students.index') }}" class="btn btn-o btn-sm"><i class="fas fa-arrow-left"></i> Back</a><a href="{{ route('admin.students.edit',$student->id) }}" class="btn btn-p btn-sm"><i class="fas fa-edit"></i> Edit</a></div>
<div class="g2">
<div class="card an"><div class="ch"><div class="av av-m">{{ strtoupper(substr($student->first_name,0,1)) }}</div><div><h2>{{ $student->full_name }}</h2><div class="tm" style="font-size:12px;">{{ $student->student_id }}</div></div><div class="ch-acts"><span class="badge {{ $student->status==='Active'?'b-s':'b-d' }}">{{ $student->status }}</span></div></div>
<div class="cb">@foreach([['Course',$student->course],['Year Level',$student->year_level],['GWA',number_format($student->gwa??0,2)],['Enrollment',$student->enrollment_type],['Email',$student->email??'—'],['Contact',$student->contact_number??'—']] as $d)
<div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--bd);font-size:13px;"><span class="tm">{{ $d[0] }}</span><span class="fws">{{ $d[1] }}</span></div>
@endforeach</div></div>
<div class="card an"><div class="ch"><i class="fas fa-file-alt" style="color:var(--gm);"></i><h2>Applications ({{ $student->applications->count() }})</h2></div>
<div class="cb" style="padding:0;">
@forelse($student->applications as $app)
<div style="display:flex;align-items:center;gap:10px;padding:12px 18px;border-bottom:1px solid var(--bd);">
<div style="flex:1;"><div class="fws" style="font-size:13px;">{{ $app->scholarship->name??'—' }}</div><div class="mono tm" style="font-size:11px;">AI: {{ $app->ai_score??0 }}% · {{ $app->ai_eligibility??'—' }}</div></div>
<span class="badge {{ $app->status==='Approved'?'b-s':($app->status==='Rejected'?'b-d':'b-w') }}">{{ $app->status }}</span>
</div>
@empty
<div style="padding:18px;text-align:center;color:var(--tm);font-size:13px;">No applications.</div>
@endforelse
</div></div>
</div>
</div>
@endsection
