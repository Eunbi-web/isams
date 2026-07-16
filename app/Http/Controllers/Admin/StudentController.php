<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Models\StudentsImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller {
    public function lookupFromImported(Request $request) {
        $data = $request->validate([
            'query' => 'required|string|max:255',
        ]);

        $q = trim($data['query']);

        // Match by student_id OR name (first/last) from the NEW import staging table.
        // Uses the most recent import_batch_id first.
        $student = StudentsImport::query()
            ->when($q !== '', function($query) use ($q) {
                $query->where(function($qq) use ($q) {
                    $qq->where('student_id', 'like', "%{$q}%")
                        ->orWhere('first_name', 'like', "%{$q}%")
                        ->orWhere('last_name', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('import_batch_id')
            ->first();


        if (!$student) {
            return response()->json([
                'ok' => false,
                'message' => 'Student not found in import list.',
            ], 404);
        }

        return response()->json([
            'ok' => true,
            'student' => [
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'student_id' => $student->student_id,
            ]
        ]);
    }

    public function index(Request $r) {

        $students = Student::when($r->search, function($q) use ($r) {
            $s = $r->search;
            $q->where(function($qq) use ($s) {
                $qq->where('first_name','like',"%$s%")
                    ->orWhere('last_name','like',"%$s%")
                    ->orWhere('student_id','like',"%$s%");
            });
        })->latest()->paginate(20);

        return view('admin.students.index', compact('students'));
    }

    public function create()  { return view('admin.students.create'); }

    public function store(Request $r) {
        $data = $r->validate([
            'first_name'         => 'required|string|max:100',
            'middle_name'        => 'nullable|string|max:100',
            'last_name'          => 'required|string|max:100',
            'student_id'         => 'required|string|unique:students,student_id|max:30',
            'course'             => 'required|string|max:100',
            'year_level'         => 'required|string|max:30',

            // Fields present on the Add Student form
            'gwa'                => 'nullable|numeric|min:1|max:5',
            'enrollment_type'    => 'nullable|string|max:50',
            'contact_number'     => 'nullable|string|max:30',
            'email'              => 'nullable|email|max:255',
        ]);

        $data['status'] = $data['status'] ?? 'Active';


        $student = Student::create($data);

        // Create student login credentials
        $this->ensureStudentUser($student);

        return redirect()->route('admin.students.index')->with('success','Student added!');
    }

    public function show(Student $student)  { return view('admin.students.show',compact('student')); }
    public function edit(Student $student)  { return view('admin.students.edit',compact('student')); }

    public function update(Request $r, Student $student) {
        $student->update($r->all());
        $this->ensureStudentUser($student);
        return redirect()->route('admin.students.show',$student)->with('success','Updated!');
    }

    public function destroy(Student $student) {
        $student->delete();
        return redirect()->route('admin.students.index')->with('success','Deleted.');
    }

    public function export() { return response()->json(Student::all()); }

    public function importForm() {
        return view('admin.students.import');
    }

    public function import(Request $request) {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,text/plain',
        ]);

        $path = $request->file('file')->getRealPath();
        $handle = fopen($path, 'r');
        if (!$handle) {
            return back()->with('error','Unable to read CSV file.');
        }

        // Read header row
        $header = fgetcsv($handle);
        if (!$header || count($header) < 2) {
            fclose($handle);
            return back()->with('error','CSV header row is missing or invalid.');
        }

        $header = array_map(fn($h) => strtolower(trim((string)$h)), $header);

        // Required import columns (must match CSV header)
        $required = ['student_id', 'first_name', 'middle_name', 'last_name'];
        $missing = array_values(array_diff($required, $header));
        if (!empty($missing)) {
            fclose($handle);
            return back()->with('error','Missing required CSV headers: '.implode(', ', $missing));
        }

        $indexByHeader = array_flip($header);


        $totalRows = 0;
        $createdUsers = 0;
        $updatedStudents = 0;
        $failedRows = 0;
        $errors = [];

        $existingStudents = Student::withTrashed()->get()->keyBy('student_id');

        // Each CSV upload creates its own import batch.
        $importBatchId = uniqid('import_', true);


                $rowNum = 1; // header is row 1

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            $totalRows++;

            try {
                $get = function(string $col) use ($row, $indexByHeader) {
                    $i = $indexByHeader[$col] ?? null;
                    if ($i === null) return null;
                    return isset($row[$i]) ? trim((string)$row[$i]) : null;
                };

                $studentId = trim((string)$get('student_id'));
                $first = (string)$get('first_name');
                $middle = $get('middle_name') ?? null;
                $last = (string)$get('last_name');

                if ($studentId === '') {
                    throw new \Exception('student_id is empty');
                }
                if ($first === '' || $last === '') {
                    throw new \Exception('first_name and last_name must not be empty.');
                }

                // Store row in staging/import table (only the 4 columns)
                StudentsImport::updateOrCreate(
                    [
                        'import_batch_id' => $importBatchId,
                        'student_id'      => $studentId,
                    ],
                    [
                        'first_name' => $first,
                        'middle_name'=> $middle,
                        'last_name'  => $last,
                    ]
                );

                // Keep downstream behavior: update real Student only if it already exists.
                $student = $existingStudents->get($studentId);
                if ($student) {
                    $student->update([
                        'first_name' => $first,
                        'middle_name' => $middle,
                        'last_name' => $last,
                    ]);
                    $updatedStudents++;

                    // Create/update login user
                    $createdBefore = false;
                    $user = $this->ensureStudentUser($student, $createdBefore);
                    if ($createdBefore) {
                        $createdUsers++;
                    }
                }




            } catch (\Throwable $e) {
                $failedRows++;
                $errors[] = ['row' => $rowNum, 'message' => $e->getMessage()];
            }
        }

        fclose($handle);

        $summary = [
            'total_rows' => $totalRows,
            'created_users' => $createdUsers,
            'updated_students' => $updatedStudents,
            'failed_rows' => $failedRows,
            'errors' => $errors,
        ];

        return view('admin.students.import', compact('summary'));
    }

    private function studentLoginEmail(Student $student): string {
        $first = strtolower(trim((string)$student->first_name));
        $last  = strtolower(trim((string)$student->last_name));
        return $first . '.' . $last . '@sccpag.edu.ph';
    }

    private function studentLoginPassword(Student $student): string {
        return 'scc' . (string)$student->student_id;
    }

    // Creates the users_all student login row if missing, and links to students.user_id.
    // If $createdFlag is provided, it will be set to true when a new user is created.
    private function ensureStudentUser(Student $student, ?bool &$createdFlag = null): ?User {
        $email = $this->studentLoginEmail($student);
        $password = $this->studentLoginPassword($student);

        $user = User::where('email', $email)->first();
        if (!$user) {
            $createdFlag = true;
            $user = User::create([
                'name' => $student->full_name ?? ($student->first_name.' '.$student->last_name),
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'student',
                'department' => $student->course,
                'contact_number' => $student->contact_number,
                'is_active' => true,
            ]);
        } else {
            $createdFlag = false;

            // Keep password aligned with rule (optional but useful)
            $user->password = Hash::make($password);
            $user->name = $student->full_name ?? $user->name;
            $user->department = $student->course;
            $user->contact_number = $student->contact_number;
            $user->role = 'student';
            $user->is_active = true;
            $user->save();
        }

        if (!$student->user_id) {
            $student->user_id = $user->id;
            $student->save();
        }

        return $user;
    }
}

