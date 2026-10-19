<?php
namespace App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Controller;
use App\Models\{User,Student,StudentsImport};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller {
    public function index(Request $r) {
        $users=User::with('student')->when($r->search,fn($q,$s)=>$q->where('name','like',"%$s%")->orWhere('email','like',"%$s%"))->when($r->role,fn($q,$role)=>$q->where('role',$role))->latest()->paginate(20);
        return view('superadmin.users.index',compact('users'));
    }
    public function create() { return view('superadmin.users.create'); }
    public function store(Request $r) {
        $data=$r->validate(['name'=>'required|string|max:200','email'=>'required|email|unique:users_all','password'=>'required|min:8|confirmed','role'=>'required|in:superadmin,admin,officer,counselor,scholarship,student','department'=>'nullable|string|max:200']);
        $data['password']=Hash::make($data['password']); $data['is_active']=true;
        $user=User::create($data);
        if ($data['role']==='student'&&$r->filled('student_id')) {
            $name=explode(' ',$data['name']);
            Student::create(['user_id'=>$user->id,'first_name'=>$name[0],'last_name'=>implode(' ',array_slice($name,1))?:'Unknown','student_id'=>$r->student_id,'course'=>$r->course??'N/A','year_level'=>$r->year_level??'1st Year','gwa'=>$r->gwa??null,'enrollment_type'=>$r->enrollment_type??'Regular','academic_year'=>'2023-2024','semester'=>'2nd','status'=>'Active']);
        }
        return redirect()->route('superadmin.users.index')->with('success','User created and role assigned!');
    }
    public function importForm() {
        return view('superadmin.users.import');
    }

    public function import(Request $r) {
        $r->validate(['file' => 'required|file|mimes:csv,txt,text/plain']);
        $summary = $this->importCsvFromFile($r->file('file')->getRealPath());
        return view('superadmin.users.import', compact('summary'));
    }

    /**
     * Bulk-import students from a CSV (EDP No., First Name, [Middle Name,] Last Name)
     * and create a student login for every row:
     *   Username: firstname.lastname@sccpag.edu.ph (lowercase, no spaces)
     *   Password: scc{EDP No.}
     * Writes are batched so large lists finish even under serverless timeouts,
     * and re-importing the same list is a no-op for rows already in sync.
     */
    public function importCsvFromFile(string $path): array {
        $handle = @fopen($path, 'r');
        if (!$handle) {
            return ['error' => 'Unable to read CSV file.'];
        }

        $header = @fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return ['error' => 'CSV file is empty or the header row is missing.'];
        }

        // Header matching is flexible about case, dots and spacing
        // ("EDP No." == "edp_no" == "EDP NO"), so real-registrar exports work as-is.
        $normalize = fn($h) => trim((string)preg_replace('/[^a-z0-9 ]/', '', strtolower(trim((string)$h))));
        $header = array_map($normalize, $header);
        $col = [];
        foreach ($header as $i => $h) {
            if (in_array($h, ['edp no', 'edp', 'edpno', 'edp number', 'student id', 'studentid', 'student number'], true)) $col['edp']    ??= $i;
            elseif (in_array($h, ['first name', 'firstname', 'given name'], true))                                       $col['first']   ??= $i;
            elseif (in_array($h, ['middle name', 'middlename'], true))                                                   $col['middle']  ??= $i;
            elseif (in_array($h, ['last name', 'lastname', 'surname', 'family name'], true))                             $col['last']    ??= $i;
        }
        if (!isset($col['edp'], $col['first'], $col['last'])) {
            fclose($handle);
            return ['error' => 'CSV must have "EDP No.", "First Name" and "Last Name" columns. Headers found: '.implode(', ', $header)];
        }

        // Lowercase with no whitespace anywhere in either name part.
        $slug = fn($s) => mb_strtolower((string)preg_replace('/\s+/u', '', trim((string)$s)));

        $rows = [];
        $staging = [];
        $seenEdp = [];
        $seenEmail = [];
        $summary = [
            'total_rows'       => 0,
            'accounts_created' => 0,
            'accounts_updated' => 0,
            'students_created' => 0,
            'students_updated' => 0,
            'unchanged'        => 0,
            'failed_rows'      => 0,
            'errors'           => [],
        ];
        $fail = function (int $rowNum, string $edp, string $message) use (&$summary) {
            $summary['failed_rows']++;
            $summary['errors'][] = ['row' => $rowNum, 'edp' => $edp, 'message' => $message];
        };

        $rowNum = 1;
        $now = now();
        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            $edp    = isset($row[$col['edp']])    ? trim((string)$row[$col['edp']])    : '';
            $first  = isset($row[$col['first']])  ? trim((string)$row[$col['first']])  : '';
            $last   = isset($row[$col['last']])   ? trim((string)$row[$col['last']])   : '';
            $middle = isset($col['middle'], $row[$col['middle']]) ? trim((string)$row[$col['middle']]) : '';
            if ($edp === '' && $first === '' && $last === '') {
                continue; // blank trailing line
            }
            $summary['total_rows']++;

            if ($edp === '')  { $fail($rowNum, '', 'EDP No. is empty'); continue; }
            if ($first === '' || $last === '') {
                $fail($rowNum, $edp, 'First Name and Last Name must not be empty');
                continue;
            }
            if (isset($seenEdp[$edp])) {
                $fail($rowNum, $edp, "Duplicate EDP No. — already imported in this file at row {$seenEdp[$edp]}");
                continue;
            }

            $email = $slug($first).'.'.$slug($last).'@sccpag.edu.ph';
            if (isset($seenEmail[$email])) {
                $fail($rowNum, $edp, "Username {$email} would collide with EDP {$seenEmail[$email]} — resolve manually");
                continue;
            }
            $seenEdp[$edp] = $rowNum;
            $seenEmail[$email] = $edp;
            $rows[] = ['row' => $rowNum, 'edp' => $edp, 'first' => $first, 'middle' => $middle, 'last' => $last,
                       'email' => $email, 'password' => 'scc'.$edp,
                       'name' => implode(' ', array_filter([$first, $middle, $last]))];

            $staging[] = [
                'import_batch_id' => null, // filled below, batch id is created after parsing
                'student_id'      => $edp,
                'first_name'      => $first,
                'middle_name'     => $middle ?: null,
                'last_name'       => $last,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }
        fclose($handle);

        if (empty($rows)) {
            $summary['error'] = 'No valid data rows found in the CSV file.';
            return $summary;
        }

        // users_all.email is uniquely indexed; one indexed fetch covers the whole list.
        $usersByEmail = User::whereIn('email', array_keys($seenEmail))->get()->keyBy('email');

        // Student records may use bare EDP or legacy "YYYY-EDP" IDs; index both.
        $studentByExact = [];
        $studentByLegacy = [];
        $ambiguousLegacy = [];
        foreach (Student::withTrashed()->get(['id', 'user_id', 'student_id', 'first_name', 'middle_name', 'last_name', 'course', 'email']) as $s) {
            $studentByExact[$s->student_id] = $s;
            if (preg_match('/-(\d+)$/', $s->student_id, $m)) {
                if (isset($studentByLegacy[$m[1]]) && $studentByLegacy[$m[1]] !== $s->student_id) {
                    $ambiguousLegacy[$m[1]] = true;
                } else {
                    $studentByLegacy[$m[1]] = $s->student_id;
                }
            }
        }

        // Pass 1: classify every row in memory; only real changes become writes.
        $userCreations = [];
        $userUpdates = [];
        $studentCreations = []; // keyed by EDP; user_id is filled after the user batch insert
        $studentUpdates = [];   // keyed by student row id
        $hasMiddle = isset($col['middle']);

        foreach ($rows as $row) {
            $user = $usersByEmail->get($row['email']);
            if ($user && $user->role !== 'student') {
                $fail($row['row'], $row['edp'], "Email {$row['email']} already belongs to a {$user->role} account — resolve manually");
                continue;
            }

            $student = $studentByExact[$row['edp']] ?? null;
            if (!$student) {
                $legacyId = $studentByLegacy[$row['edp']] ?? null;
                $student = $legacyId !== null && empty($ambiguousLegacy[$row['edp']]) ? $studentByExact[$legacyId] : null;
            }

            $changed = false;
            if (!$user) {
                $user = (object) [
                    'id' => null,
                    'email' => $row['email'],
                    'password' => Hash::make($row['password']),
                    'role' => 'student',
                    'is_active' => true,
                    'department' => $student->course ?? 'N/A',
                    'name' => $row['name'],
                ];
                $userCreations[$row['email']] = $user;
                $usersByEmail->put($row['email'], $user);
                $changed = true;
            } elseif (!Hash::check($row['password'], $user->password)) {
                // Keep the documented scc{EDP} credential on existing accounts.
                $userUpdates[$user->id] = ['password' => Hash::make($row['password'])];
                $user->password = $userUpdates[$user->id]['password'];
                $changed = true;
            }

            if ($student) {
                $attrs = [];
                if (trim($student->first_name ?? '') !== $row['first'])  { $attrs['first_name'] = $row['first']; }
                if (trim($student->last_name ?? '') !== $row['last'])    { $attrs['last_name'] = $row['last']; }
                if ($hasMiddle && trim((string)$student->middle_name) !== $row['middle']) { $attrs['middle_name'] = $row['middle'] ?: null; }
                if (empty($student->email))                              { $attrs['email'] = $row['email']; }
                $pendingLink = $user->id === null; // account is created in this same batch
                if ((int)($student->user_id ?? 0) !== (int)($user->id ?? 0) || $pendingLink) {
                    // The user id may still be pending a batch insert; _user_email resolves it in pass 2.
                    $attrs['user_id'] = $user->id;
                    if ($pendingLink) { $attrs['_user_email'] = $row['email']; }
                }
                if ($attrs) {
                    $attrs['updated_at'] = $now;
                    $studentUpdates[$student->id] = $attrs;
                    $student->user_id = $attrs['user_id'] ?? $student->user_id;
                    $student->email = $attrs['email'] ?? $student->email;
                    $changed = true;
                }
            } else {
                $studentCreations[$row['edp']] = [
                    'user_id'     => null,
                    'first_name'  => $row['first'],
                    'middle_name' => $row['middle'] ?: null,
                    'last_name'   => $row['last'],
                    'student_id'  => $row['edp'],
                    'course'      => 'N/A',
                    'year_level'  => 'N/A',
                    'email'       => $row['email'],
                    'status'      => 'Active',
                ];
                $changed = true;
            }

            if (!$changed) { $summary['unchanged']++; }
        }
        $summary['accounts_created'] = count($userCreations);
        $summary['students_created'] = count($studentCreations);

        // Pass 2: write, newest tables first so student rows can link to user ids.
        DB::beginTransaction();
        try {
            $batch = uniqid('import_', true);
            foreach ($staging as &$s) { $s['import_batch_id'] = $batch; }
            foreach (array_chunk($staging, 500) as $chunk) {
                DB::table('students_imports')->insert($chunk);
            }
            unset($s, $chunk);

            if ($userCreations) {
                DB::table('users_all')->insert(array_map(fn($u) => [
                    'name'       => $u->name,
                    'email'      => $u->email,
                    'password'   => $u->password,
                    'role'       => $u->role,
                    'department' => $u->department,
                    'is_active'  => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ], $userCreations));
                foreach (User::whereIn('email', array_keys($userCreations))->get(['id', 'email']) as $fresh) {
                    $usersByEmail->get($fresh->email)->id = $fresh->id;
                }
            }

            if ($studentCreations) {
                $srows = [];
                foreach ($studentCreations as $edp => $sc) {
                    $sc['user_id'] = $usersByEmail->get($sc['email'])->id;
                    $sc['created_at'] = $sc['updated_at'] = $now;
                    $srows[] = $sc;
                }
                foreach (array_chunk($srows, 500) as $chunk) {
                    DB::table('students')->insert($chunk);
                }
            }

            foreach ($studentUpdates as $id => $attrs) {
                if (array_key_exists('_user_email', $attrs)) {
                    $attrs['user_id'] = $usersByEmail->get($attrs['_user_email'])->id;
                    unset($attrs['_user_email']);
                }
                DB::table('students')->where('id', $id)->update($attrs);
            }
            foreach ($userUpdates as $id => $attrs) {
                DB::table('users_all')->where('id', $id)->update($attrs + ['updated_at' => $now]);
            }
            $summary['students_updated'] = count($studentUpdates);
            $summary['accounts_updated'] = count($userUpdates);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $summary['error'] = 'Import failed and was rolled back: '.$e->getMessage();
            return $summary;
        }

        return $summary;
    }

    public function edit(User $user) { return view('superadmin.users.edit',compact('user')); }
    public function update(Request $r, User $user) {
        $data=$r->validate(['name'=>'required|string|max:200','email'=>'required|email|unique:users_all,email,'.$user->id,'role'=>'required|in:superadmin,admin,officer,counselor,scholarship,student','department'=>'nullable|string|max:200','is_active'=>'nullable']);
        if ($r->filled('password')) { $r->validate(['password'=>'min:8|confirmed']); $data['password']=Hash::make($r->password); }
        $data['is_active']=$r->boolean('is_active',true);
        $user->update($data);
        return redirect()->route('superadmin.users.index')->with('success','User updated!');
    }
    public function toggleActive(User $user) {
        if ($user->role==='superadmin') return back()->with('error','Cannot deactivate superadmin.');
        $user->update(['is_active'=>!$user->is_active]);
        return back()->with('success','Account '.($user->is_active?'activated':'deactivated').'.');
    }
    public function destroy(User $user) {
        if ($user->role==='superadmin') return back()->with('error','Cannot delete superadmin.');
        $user->delete();
        return redirect()->route('superadmin.users.index')->with('success','User deleted.');
    }
}
