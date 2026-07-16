@extends('layouts.app')
@section('title', 'Scholarship Grantees')
@section('page-title', 'Scholarship Grantees')
@section('content')
<div class="card animate"><div class="card-header"><i class="fas fa-users" style="color:var(--accent);"></i>
<h2>{{ isset($scholarship) ? $scholarship->name : 'Scholarship' }} — Grantees</h2>
<div class="card-actions"><a href="{{ route('scholarships.index') }}" class="btn btn-outline btn-sm">Back</a></div>
</div><div class="card-body"><p class="text-muted">List of grantees for this scholarship program.</p></div></div>
@endsection
