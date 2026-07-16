<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DisciplineCase;
use App\Models\Student;
use App\Models\StudentsImport;
use Illuminate\Http\Request;

class DisciplineAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = DisciplineCase::with('student');

        if ($request->filled('search')) {
            $s = $request->string('search')->trim();

            $query->where(function ($q) use ($s) {
                $q->where('case_number', 'like', "%{$s}%")
                    ->orWhere('description', 'like', "%{$s}%")
                    ->orWhere('violation_type', 'like', "%{$s}%")
                    ->orWhereHas('student', function ($sq) use ($s) {
                        $sq->where('student_id', 'like', "%{$s}%")
                            ->orWhere('first_name', 'like', "%{$s}%")
                            ->orWhere('last_name', 'like', "%{$s}%")
                            ->orWhereRaw('CONCAT(first_name," ",last_name) LIKE ?', ["%{$s}%"]);
                    });
            });
        }

        if ($request->filled('offense_category')) {
            $query->where('violation_type', $request->string('offense_category')->trim()->toString());
        }

        $sort = $request->string('sort')->trim()->toString();
        $direction = $request->string('direction')->trim()->toString();
        if (!in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'desc';
        }

        if ($sort === 'incident_date') {
            $query->orderBy('incident_date', $direction);
        } elseif ($sort === 'date_filed') {
            $query->orderBy('created_at', $direction);
        } else {
            $query->latest();
        }

        $cases = $query->paginate(20)->withQueryString();

        return view('admin.discipline.index', compact('cases'));
    }

    public function create()
    {
        // Keep existing dropdown for now, but also allow lookup by EDP from students_imports.
        $students = Student::orderBy('last_name')
            ->get(['id', 'student_id', 'first_name', 'middle_name', 'last_name', 'course', 'contact_number', 'guardian_name']);

        return view('admin.discipline.create', compact('students'));
    }

    /**
     * Lookup student information by EDP (students_imports.student_id).
     * Used by Discipline Records > Add Record form.
     */
    public function lookupEdp(Request $request)
    {
        $data = $request->validate([
            'edp' => 'required|string|max:255',
        ]);

        $edp = trim($data['edp']);

        $import = StudentsImport::query()
            ->where('student_id', $edp)
            ->orWhere('student_id', 'like', $edp . '%')
            ->first();

        if (!$import) {
            return response()->json([
                'ok' => false,
                'message' => 'Student not found.',
            ], 404);
        }

        return response()->json([
            'ok' => true,
            'name' => $import->getFullNameAttribute(),
        ]);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'offense_category' => 'required|in:Major,Minor',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $student = Student::query()->findOrFail($data['student_id']);

        // Auto-generate case number
        $year = now()->year;
        $count = DisciplineCase::whereYear('created_at', $year)->count() + 1;
        $caseNumber = sprintf('%d-DC-%03d', $year, $count);

        DisciplineCase::create([
            'case_number' => $caseNumber,
            'student_id' => $student->id,
            // Category (Major/Minor) is stored in existing column
            'violation_type' => $data['offense_category'],
            // Requested "Date" corresponds to incident_date
            'incident_date' => $data['date'],
            'description' => $data['description'] ?? null,
            // Defaults required by model fillable + existing controller logic
            'status' => 'Under Investigation',
            'filed_by' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.discipline.index')
            ->with('success', 'Discipline record added successfully.');
    }
}

