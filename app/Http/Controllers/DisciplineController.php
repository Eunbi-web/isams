<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DisciplineCase;
use App\Models\Student;

class DisciplineController extends Controller
{
    public function index(Request $request)
    {
        $cases = DisciplineCase::with('student')
            ->when($request->search, fn($q, $s) =>
                $q->whereHas('student', fn($sq) =>
                    $sq->where('first_name', 'like', "%$s%")
                       ->orWhere('last_name', 'like', "%$s%"))
                  ->orWhere('case_number', 'like', "%$s%"))
            ->when($request->violation, fn($q, $v) => $q->where('violation_type', $v))
            ->when($request->status,    fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(20);

        return view('discipline.index', compact('cases'));
    }

    public function create()
    {
        $students = Student::orderBy('last_name')->get();
        return view('discipline.create', compact('students'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id'     => 'required|exists:students,id',
            'violation_type' => 'required|string|max:100',
            'incident_date'  => 'required|date',
            'description'    => 'nullable|string',
            'witnesses'      => 'nullable|string',
            'status'         => 'required|string|max:50',
            'hearing_date'   => 'nullable|date',
            'sanction'       => 'nullable|string|max:200',
        ]);

        // Auto-generate case number
        $year = now()->year;
        $count = DisciplineCase::whereYear('created_at', $year)->count() + 1;
        $data['case_number'] = sprintf('%d-DC-%03d', $year, $count);

        DisciplineCase::create($data);

        return redirect()->route('discipline.index')
            ->with('success', 'Discipline case filed successfully!');
    }

    public function show(DisciplineCase $discipline)
    {
        $discipline->load('student');
        return view('discipline.show', compact('discipline'));
    }

    public function edit(DisciplineCase $discipline)
    {
        $students = Student::orderBy('last_name')->get();
        return view('discipline.edit', compact('discipline', 'students'));
    }

    public function update(Request $request, DisciplineCase $discipline)
    {
        $data = $request->validate([
            'violation_type' => 'required|string|max:100',
            'incident_date'  => 'required|date',
            'description'    => 'nullable|string',
            'status'         => 'required|string|max:50',
            'hearing_date'   => 'nullable|date',
            'sanction'       => 'nullable|string|max:200',
            'remarks'        => 'nullable|string',
        ]);

        $discipline->update($data);

        return redirect()->route('discipline.show', $discipline)
            ->with('success', 'Case updated!');
    }

    public function destroy(DisciplineCase $discipline)
    {
        $discipline->delete();
        return redirect()->route('discipline.index')
            ->with('success', 'Case deleted.');
    }

    public function updateStatus(Request $request, DisciplineCase $discipline)
    {
        $discipline->update(['status' => $request->status]);
        return back()->with('success', 'Status updated!');
    }
}
