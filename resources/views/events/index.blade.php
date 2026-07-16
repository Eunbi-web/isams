@extends('layouts.app')
@section('title', 'Events')
@section('page-title', 'Events & Activities')
@section('page-subtitle', 'Manage student events, programs and activities')

@section('content')
<div class="d-flex align-center justify-between mb-3">
    <div class="d-flex gap-2">
        <div class="search-box"><i class="fas fa-search"></i><input type="text" class="form-control" placeholder="Search events..." style="width:240px;"></div>
        <select class="form-control" style="width:160px;"><option>All Types</option><option>Leadership</option><option>Academic</option><option>Cultural</option><option>Sports</option><option>Wellness</option></select>
        <select class="form-control" style="width:150px;"><option>All Status</option><option>Upcoming</option><option>Ongoing</option><option>Completed</option><option>Cancelled</option></select>
    </div>
    <a href="{{ route('events.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Create Event</a>
</div>

<div class="card animate">
    <div class="card-header">
        <i class="fas fa-calendar-alt" style="color:var(--accent);"></i>
        <h2>All Events</h2>
        <div class="card-actions">
            <a href="#" class="btn btn-outline btn-sm"><i class="fas fa-calendar"></i> Calendar View</a>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Event</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Venue</th>
                    <th>Organizer</th>
                    <th>Participants</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach([
                    ['Leadership Summit 2024','Leadership','Apr 25, 2024','Gymnasium','SSG',320,'Upcoming'],
                    ['Mental Health Awareness Week','Wellness','Apr 28–May 3','Various','Guidance Office',600,'Ongoing'],
                    ['Organizations Fair','Activity','May 5, 2024','Covered Court','OSSD',800,'Upcoming'],
                    ['Scholarship Application Day','Academic','May 10, 2024','SAO Office','Scholarship Office',200,'Upcoming'],
                    ['Intramurals 2024','Sports','May 15–18, 2024','Sports Complex','Sports Council',1200,'Upcoming'],
                    ['Foundation Day Celebration','Cultural','Mar 15, 2024','Grounds','Student Council',2000,'Completed'],
                    ['Career Summit','Academic','Mar 22, 2024','Auditorium','OSSD',450,'Completed'],
                    ['Environmental Clean-Up Drive','Service','Apr 5, 2024','Campus & Surrounds','Environment Club',180,'Completed'],
                    ['Acquaintance Party','Social','Aug 20, 2024','Gymnasium','SSG',900,'Upcoming'],
                    ['Sportsfest Opening','Sports','Sep 2, 2024','Sports Complex','Sports Council',1500,'Upcoming'],
                ] as $e)
                <tr>
                    <td>
                        <div class="fw-semi" style="font-size:14px;">{{ $e[0] }}</div>
                    </td>
                    <td>
                        <span class="badge {{ $e[1]=='Leadership' ? 'badge-primary' : ($e[1]=='Wellness' ? 'badge-success' : ($e[1]=='Sports' ? 'badge-danger' : ($e[1]=='Academic' ? 'badge-info' : 'badge-gray'))) }}">
                            {{ $e[1] }}
                        </span>
                    </td>
                    <td class="text-muted font-mono" style="font-size:12px;">{{ $e[2] }}</td>
                    <td class="text-muted" style="font-size:13px;">{{ $e[3] }}</td>
                    <td class="text-muted" style="font-size:13px;">{{ $e[4] }}</td>
                    <td class="fw-semi text-primary">{{ number_format($e[5]) }}</td>
                    <td>
                        <span class="badge {{ $e[6]=='Upcoming' ? 'badge-primary' : ($e[6]=='Ongoing' ? 'badge-success' : ($e[6]=='Completed' ? 'badge-gray' : 'badge-danger')) }}">
                            {{ $e[6] }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="#" class="btn btn-outline btn-sm btn-icon" title="View"><i class="fas fa-eye"></i></a>
                            <a href="#" class="btn btn-outline btn-sm btn-icon" title="Edit"><i class="fas fa-edit"></i></a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
