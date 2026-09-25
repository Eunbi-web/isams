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
        return view('admin.discipline.create');
    }

    /**
     * CSV export of all discipline records, honoring the same filters as the index page.
     */
    public function export(Request $request)
    {
        $query = DisciplineCase::with('student');

        if ($request->filled('search')) {
            $s = $request->string('search')->trim()->toString();
            $query->where(function ($q) use ($s) {
                $q->where('case_number', 'like', "%{$s}%")
                    ->orWhere('description', 'like', "%{$s}%")
                    ->orWhere('violation_type', 'like', "%{$s}%")
                    ->orWhereHas('student', function ($sq) use ($s) {
                        $sq->where('student_id', 'like', "%{$s}%")
                            ->orWhere('first_name', 'like', "%{$s}%")
                            ->orWhere('last_name', 'like', "%{$s}%");
                    });
            });
        }

        if ($request->filled('offense_category')) {
            $query->where('violation_type', $request->string('offense_category')->trim()->toString());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->trim()->toString());
        }

        if ($request->filled('department')) {
            $dept = $request->string('department')->trim()->toString();
            $query->whereHas('student', function ($sq) use ($dept) {
                $sq->where('department', $dept)->orWhere('course', $dept);
            });
        }

        $fileName = 'discipline-records-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'EDP Number', 'Student Name', 'Department', 'Category of Offense', 'Description', 'Incident Date', 'Guardian Name', 'Guardian Contact', 'Status', 'Created At']);

            $query->orderBy('created_at', 'desc')->chunk(500, function ($cases) use ($out) {
                foreach ($cases as $c) {
                    $s = $c->student;
                    fputcsv($out, [
                        $c->id,
                        $s?->student_id,
                        $s?->full_name,
                        $s?->department ?: $s?->course,
                        $c->violation_type,
                        $c->description,
                        $c->incident_date?->format('Y-m-d'),
                        $s?->guardian_name,
                        $s?->guardian_contact,
                        $c->status,
                        $c->created_at?->format('Y-m-d H:i:s'),
                    ]);
                }
            });

            fclose($out);
        }, $fileName, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Lookup student information by EDP (students_imports.student_id).
     * Used by Discipline Records > Add Record form to auto-fill
     * name, department, contact number and guardian.
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
            ->orderByRaw('CASE WHEN student_id = ? THEN 0 ELSE 1 END', [$edp])
            ->first();

        if (!$import) {
            return response()->json([
                'ok' => false,
                'message' => 'Student not found.',
            ], 404);
        }

        // Enrich with the official student record when it exists
        $student = Student::where('student_id', $import->student_id)->first();

        return response()->json([
            'ok' => true,
            'edp' => $import->student_id,
            'name' => $student?->full_name ?: $import->getFullNameAttribute(),
            'department' => $student?->department,
            'course' => $student?->course,
            'contact_number' => $student?->contact_number,
            'guardian_name' => $student?->guardian_name,
        ]);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'edp' => 'required|string|max:50',
            'offense_category' => 'required|in:Major,Minor',
            'department' => 'nullable|in:CTEAS,CBE,CCS,COC',
            'contact_number' => 'nullable|string|max:30',
            'guardian_name' => 'nullable|string|max:150',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        // Resolve the student by EDP number (exact match first)
        $import = StudentsImport::where('student_id', $data['edp'])->first();

        $student = Student::where('student_id', $data['edp'])->first();

        // Student not yet in the official table — create it from the import record
        if (!$student) {
            if (!$import) {
                return back()
                    ->withInput()
                    ->with('error', "No student found with EDP {$data['edp']}. Check the EDP number and try again.");
            }

            $student = Student::create([
                'student_id'      => $import->student_id,
                'first_name'      => $import->first_name,
                'middle_name'     => $import->middle_name,
                'last_name'       => $import->last_name,
                // course / year_level columns are NOT NULL with no default;
                // placeholders until the full record is encoded
                'course'          => $data['department'] ?? 'CTEAS',
                'year_level'      => '1st Year',
                'department'      => $data['department'] ?? null,
                'contact_number'  => $data['contact_number'] ?? null,
                'guardian_name'   => $data['guardian_name'] ?? null,
                'enrollment_type' => 'Regular',
                'status'          => 'Active',
            ]);
        } else {
            // Keep the student's department/contact/guardian up to date from the form
            $student->update(array_filter([
                'department'     => $data['department'] ?? null,
                'contact_number' => $data['contact_number'] ?? null,
                'guardian_name'  => $data['guardian_name'] ?? null,
            ]));
        }

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
            ->with('success', "Discipline record {$caseNumber} added successfully for {$student->full_name}.");
    }
}

