@extends('layouts.app')
@section('title', 'Create Event')
@section('page-title', 'Create Event')
@section('page-subtitle', 'Schedule a new student event or activity')
@section('content')
<div style="max-width:760px;">
<form method="POST" action="{{ route('events.store') }}">
@csrf
<div class="card animate mb-3">
<div class="card-header"><i class="fas fa-calendar-plus" style="color:var(--accent);"></i><h2>Event Details</h2></div>
<div class="card-body">
<div class="form-group"><label class="form-label">Event Title *</label><input type="text" name="title" class="form-control" placeholder="e.g. Leadership Summit 2024" required></div>
<div class="grid-2">
<div class="form-group"><label class="form-label">Event Type *</label>
<select name="type" class="form-control" required>
<option value="">Select type</option>
@foreach(['Leadership','Academic','Cultural','Sports','Wellness','Service','Social','Activity'] as $t)
<option>{{ $t }}</option>
@endforeach
</select></div>
<div class="form-group"><label class="form-label">Status</label>
<select name="status" class="form-control">
<option>Upcoming</option><option>Ongoing</option>
</select></div>
</div>
<div class="grid-2">
<div class="form-group"><label class="form-label">Event Date *</label><input type="date" name="event_date" class="form-control" required></div>
<div class="form-group"><label class="form-label">End Date</label><input type="date" name="end_date" class="form-control"></div>
</div>
<div class="grid-2">
<div class="form-group"><label class="form-label">Venue</label><input type="text" name="venue" class="form-control" placeholder="Gymnasium, Auditorium..."></div>
<div class="form-group"><label class="form-label">Organizer</label><input type="text" name="organizer" class="form-control" placeholder="SSG, OSSD..."></div>
</div>
<div class="form-group"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"></textarea></div>
</div></div>
<div class="d-flex gap-2">
<button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Create Event</button>
<a href="{{ route('events.index') }}" class="btn btn-outline">Cancel</a>
</div>
</form></div>
@endsection
