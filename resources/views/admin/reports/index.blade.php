@extends('admin.layouts.app')
@section('title','Reports')
@section('page-title','Reports & Analytics')
@section('page-sub','Download scholarship and student reports')
@section('content')
@php
$eligible=\App\Models\ScholarshipApplication::where('ai_eligibility','Eligible')->count();
$total=\App\Models\ScholarshipApplication::count();
$approved=\App\Models\ScholarshipApplication::where('status','Approved')->count();
$students=\App\Models\Student::count();
@endphp
<div class="sg">
<div class="sc an"><div class="si g"><i class="fas fa-check-circle"></i></div><div class="sv"><div class="lbl">Eligible</div><div class="val">{{ $eligible }}</div><div class="chg">AI-verified</div></div></div>
<div class="sc an"><div class="si y"><i class="fas fa-file-alt"></i></div><div class="sv"><div class="lbl">Total Apps</div><div class="val">{{ $total }}</div></div></div>
<div class="sc an"><div class="si dg"><i class="fas fa-award"></i></div><div class="sv"><div class="lbl">Approved</div><div class="val">{{ $approved }}</div></div></div>
<div class="sc an"><div class="si t"><i class="fas fa-users"></i></div><div class="sv"><div class="lbl">Students</div><div class="val">{{ $students }}</div></div></div>
</div>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:14px;">
@foreach([['Scholarship Applications','All application records with AI scores','applications','fas fa-file-alt','g'],['Eligible Students','All AI-eligible applicants','eligible','fas fa-check-circle','dg'],['Scholarship Programs','Program details and slot counts','scholarships','fas fa-award','y'],['Student Records','Complete student academic records','students','fas fa-users','t']] as $r)
<div class="card an"><div class="ch"><div class="si {{ $r[4] }}" style="width:36px;height:36px;font-size:14px;border-radius:9px;"><i class="{{ $r[3] }}"></i></div><h2>{{ $r[0] }}</h2></div>
<div class="cb"><div class="tm" style="font-size:13px;margin-bottom:14px;">{{ $r[1] }}</div>
<a href="{{ route('admin.reports.download',$r[2]) }}" class="btn btn-p btn-sm"><i class="fas fa-download"></i> Download Report</a>
</div></div>
@endforeach
</div>
@endsection
