<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $students = Student::query()
            ->when($request->search, fn($q, $s) => $q->where('first_name', 'like', "%$s%")
                ->orWhere('last_name', 'like', "%$s%")
                ->orWhere('student_id', 'like', "%$s%"))
            ->when($request->course,     fn($q, $c) => $q->where('course', $c))
            ->when($request->year_level, fn($q, $y) => $q->where('year_level', $y))
            ->latest()
            ->paginate(20);

        return view('students.index', compact('students'));
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name'             => 'required|string|max:100',
            'middle_name'            => 'nullable|string|max:100',
            'last_name'              => 'required|string|max:100',
            'student_id'             => 'required|string|unique:students,student_id|max:30',
            'course'                 => 'required|string|max:100',
            'year_level'             => 'required|string|max:30',
            'section'                => 'nullable|string|max:10',
            'academic_year'          => 'required|string|max:20',
            'semester'               => 'required|string|max:20',
            'enrollment_type'        => 'required|string|max:30',
            'gwa'                    => 'nullable|numeric|min:1|max:5',
            'birthdate'              => 'nullable|date',
            'sex'                    => 'nullable|string|max:10',
            'civil_status'           => 'nullable|string|max:20',
            'contact_number'         => 'nullable|string|max:20',
            'email'                  => 'nullable|email|max:150',
            'address'                => 'nullable|string|max:500',
            'guardian_name'          => 'nullable|string|max:200',
            'guardian_relationship'  => 'nullable|string|max:50',
            'guardian_contact'       => 'nullable|string|max:20',
        ]);

        $data['status'] = 'Active';
        Student::create($data);

        return redirect()->route('students.index')
            ->with('success', 'Student enrolled successfully!');
    }

    public function show(Student $student)
    {
        $student->load(['counselingSessions', 'disciplineCases', 'scholarships', 'organizations']);
        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $data = $request->validate([
            'first_name'    => 'required|string|max:100',
            'middle_name'   => 'nullable|string|max:100',
            'last_name'     => 'required|string|max:100',
            'course'        => 'required|string|max:100',
            'year_level'    => 'required|string|max:30',
            'section'       => 'nullable|string|max:10',
            'academic_year' => 'required|string|max:20',
            'semester'      => 'required|string|max:20',
            'gwa'           => 'nullable|numeric|min:1|max:5',
            'contact_number'=> 'nullable|string|max:20',
            'email'         => 'nullable|email|max:150',
            'address'       => 'nullable|string|max:500',
            'status'        => 'required|string|max:30',
        ]);

        $student->update($data);

        return redirect()->route('students.show', $student)
            ->with('success', 'Student record updated!');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students.index')
            ->with('success', 'Student record deleted.');
    }

    public function export()
    {
        $students = Student::orderBy('last_name')->get();
        // In production: use Laravel Excel or Barryvdh/Laravel-DomPDF
        return response()->json($students);
    }
}
