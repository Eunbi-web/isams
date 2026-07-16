<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CounselingSession;
use App\Models\Student;
use App\Models\User;

class CounselingController extends Controller
{
    public function index(Request $request)
    {
        $sessions = CounselingSession::with(['student', 'counselor'])
            ->when($request->search, fn($q, $s) =>
                $q->whereHas('student', fn($sq) =>
                    $sq->where('first_name', 'like', "%$s%")
                       ->orWhere('last_name', 'like', "%$s%")))
            ->when($request->type,   fn($q, $t) => $q->where('concern_type', $t))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(20);

        return view('counseling.index', compact('sessions'));
    }

    public function create()
    {
        $students  = Student::orderBy('last_name')->get();
        $counselors = User::where('role', 'counselor')->get();
        return view('counseling.create', compact('students', 'counselors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id'    => 'required|exists:students,id',
            'counselor_id'  => 'nullable|exists:users,id',
            'concern_type'  => 'required|string|max:100',
            'concern_detail'=> 'nullable|string',
            'session_date'  => 'nullable|date',
            'session_time'  => 'nullable',
            'priority'      => 'required|string|max:20',
            'status'        => 'required|string|max:30',
        ]);

        CounselingSession::create($data);

        return redirect()->route('counseling.index')
            ->with('success', 'Counseling session created!');
    }

    public function show(CounselingSession $counseling)
    {
        $counseling->load(['student', 'counselor']);
        return view('counseling.show', compact('counseling'));
    }

    public function edit(CounselingSession $counseling)
    {
        $students   = Student::orderBy('last_name')->get();
        $counselors = User::where('role', 'counselor')->get();
        return view('counseling.edit', compact('counseling', 'students', 'counselors'));
    }

    public function update(Request $request, CounselingSession $counseling)
    {
        $data = $request->validate([
            'concern_type'   => 'required|string|max:100',
            'concern_detail' => 'nullable|string',
            'session_date'   => 'nullable|date',
            'session_time'   => 'nullable',
            'priority'       => 'required|string|max:20',
            'status'         => 'required|string|max:30',
            'remarks'        => 'nullable|string',
        ]);

        $counseling->update($data);

        return redirect()->route('counseling.show', $counseling)
            ->with('success', 'Session updated!');
    }

    public function destroy(CounselingSession $counseling)
    {
        $counseling->delete();
        return redirect()->route('counseling.index')
            ->with('success', 'Session deleted.');
    }

    public function updateStatus(Request $request, CounselingSession $counseling)
    {
        $counseling->update(['status' => $request->status]);
        return back()->with('success', 'Status updated!');
    }

    public function calendar()
    {
        $sessions = CounselingSession::with('student')
            ->whereNotNull('session_date')
            ->get();
        return view('counseling.calendar', compact('sessions'));
    }
}
