@extends('admin.layouts.app')
@section('title','Scholarship')
@section('page-title','{{ $scholarship->name }}')
@section('content')
<div style="max-width:760px;">
<div style="margin-bottom:14px;display:flex;gap:8px;"><a href="{{ route('admin.scholarships.index') }}" class="btn btn-o btn-sm"><i class="fas fa-arrow-left"></i> Back</a><a href="{{ route('admin.scholarships.edit',$scholarship->id) }}" class="btn btn-p btn-sm"><i class="fas fa-edit"></i> Edit</a></div>
@include('admin.scholarships._details')
</div>
@endsection
