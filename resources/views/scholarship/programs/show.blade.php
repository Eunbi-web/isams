@extends('scholarship.layouts.app')
@section('title','Scholarship')
@section('page-title','{{ $scholarship->name }}')
@section('content')
<div style="max-width:760px;">
<div style="margin-bottom:14px;display:flex;gap:8px;"><a href="{{ route('scholarship.programs.index') }}" class="btn btn-o btn-sm"><i class="fas fa-arrow-left"></i> Back</a><a href="{{ route('scholarship.programs.edit',$scholarship->id) }}" class="btn btn-p btn-sm"><i class="fas fa-edit"></i> Edit</a></div>
@include('scholarship.programs._details')
</div>
@endsection
