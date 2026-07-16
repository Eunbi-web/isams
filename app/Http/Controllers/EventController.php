<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::when($request->search, fn($q, $s) => $q->where('title', 'like', "%$s%"))
            ->when($request->type,   fn($q, $t) => $q->where('type', $t))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->orderBy('event_date', 'desc')
            ->paginate(20);

        return view('events.index', compact('events'));
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:200',
            'type'         => 'required|string|max:50',
            'description'  => 'nullable|string',
            'event_date'   => 'required|date',
            'end_date'     => 'nullable|date',
            'time_start'   => 'nullable',
            'time_end'     => 'nullable',
            'venue'        => 'nullable|string|max:200',
            'organizer'    => 'nullable|string|max:200',
            'max_participants' => 'nullable|integer|min:0',
            'status'       => 'required|string|max:30',
        ]);

        Event::create($data);

        return redirect()->route('events.index')
            ->with('success', 'Event created!');
    }

    public function show(Event $event)
    {
        return view('events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        return view('events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:200',
            'type'        => 'required|string|max:50',
            'description' => 'nullable|string',
            'event_date'  => 'required|date',
            'venue'       => 'nullable|string|max:200',
            'status'      => 'required|string|max:30',
        ]);

        $event->update($data);

        return redirect()->route('events.show', $event)
            ->with('success', 'Event updated!');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('events.index')
            ->with('success', 'Event deleted.');
    }
}
