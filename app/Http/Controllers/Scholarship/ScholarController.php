<?php
namespace App\Http\Controllers\Scholarship;
use App\Http\Controllers\Controller;
use App\Models\Scholar;
use Illuminate\Http\Request;

class ScholarController extends Controller {
    public function index(Request $r) {
        $scholars = Scholar::query()
            ->when($r->search, function($q,$s){
                $q->where(function($qq) use ($s){
                    $qq->where('first_name','like',"%$s%")
                       ->orWhere('last_name','like',"%$s%")
                       ->orWhere('student_number','like',"%$s%")
                       ->orWhere('scholarship_name','like',"%$s%");
                });
            })
            ->when($r->status, fn($q,$v)=>$q->where('status',$v))
            ->when($r->type, fn($q,$v)=>$q->where('scholarship_type',$v))
            ->latest()
            ->paginate(20)->withQueryString();

        $stats = [
            'total'     => Scholar::count(),
            'active'    => Scholar::where('status','Active')->count(),
            'internal'  => Scholar::where('scholarship_type','Internal')->where('status','Active')->count(),
            'external'  => Scholar::where('scholarship_type','External')->where('status','Active')->count(),
            'graduated' => Scholar::where('status','Graduated')->count(),
        ];

        return view('scholarship.scholars.index', compact('scholars','stats'));
    }

    public function store(Request $r) {
        $data = $r->validate([
            'student_number'   => 'nullable|string|max:50',
            'first_name'       => 'required|string|max:100',
            'middle_name'      => 'nullable|string|max:100',
            'last_name'        => 'required|string|max:100',
            'course'           => 'nullable|string|max:100',
            'year_level'       => 'nullable|string|max:30',
            'scholarship_name' => 'required|string|max:200',
            'scholarship_type' => 'required|in:Internal,External',
            'status'           => 'required|in:Active,Inactive,Graduated',
            'remarks'          => 'nullable|string|max:1000',
        ]);
        $data['source'] = 'Manual';
        Scholar::create($data);

        if ($r->wantsJson() || $r->ajax()) {
            return response()->json(['message'=>'Scholar added successfully.','reload'=>true]);
        }
        return back()->with('success','Scholar added successfully.');
    }

    public function update(Request $r, Scholar $scholar) {
        $data = $r->validate([
            'student_number'   => 'nullable|string|max:50',
            'first_name'       => 'required|string|max:100',
            'middle_name'      => 'nullable|string|max:100',
            'last_name'        => 'required|string|max:100',
            'course'           => 'nullable|string|max:100',
            'year_level'       => 'nullable|string|max:30',
            'scholarship_name' => 'required|string|max:200',
            'scholarship_type' => 'required|in:Internal,External',
            'status'           => 'required|in:Active,Inactive,Graduated',
            'remarks'          => 'nullable|string|max:1000',
        ]);
        $scholar->update($data);

        if ($r->wantsJson() || $r->ajax()) {
            return response()->json(['message'=>'Scholar updated successfully.','reload'=>true]);
        }
        return back()->with('success','Scholar updated successfully.');
    }

    public function destroy(Request $r, Scholar $scholar) {
        $scholar->delete();
        if ($r->wantsJson() || $r->ajax()) {
            return response()->json(['message'=>'Scholar removed from the list.']);
        }
        return back()->with('success','Scholar removed from the list.');
    }

    /**
     * Import scholars from an uploaded Excel (.xlsx) or CSV file.
     * The first row must be a header row; columns are matched by name.
     */
    public function importExcel(Request $r) {
        $r->validate(['file' => 'required|file|max:5120']);

        $file = $r->file('file');
        $ext  = strtolower($file->getClientOriginalExtension());

        try {
            $rows = match ($ext) {
                'csv'        => $this->readCsv($file->getRealPath()),
                'xls', 'xlsx'=> $this->readXlsx($file->getRealPath()),
                default      => null,
            };
        } catch (\Throwable $e) {
            return response()->json(['message'=>'Could not read the file. Make sure it is a valid .xlsx or .csv file.'], 422);
        }

        if (!$rows || count($rows) < 2) {
            return response()->json(['message'=>'The file is empty or has no data rows.'], 422);
        }

        $header = array_shift($rows);
        $map = $this->headerMap($header);

        if (!isset($map['first_name']) || !isset($map['last_name'])) {
            return response()->json(['message'=>'Missing required columns. The file needs "First Name" and "Last Name" headers.'], 422);
        }

        $imported = 0; $skipped = 0;
        foreach ($rows as $row) {
            $first = trim($row[$map['first_name']] ?? '');
            $last  = trim($row[$map['last_name']] ?? '');
            if ($first === '' && $last === '') { $skipped++; continue; }

            Scholar::create([
                'student_number'   => isset($map['student_number']) ? trim($row[$map['student_number']] ?? '') ?: null : null,
                'first_name'       => $first ?: '—',
                'middle_name'      => isset($map['middle_name']) ? trim($row[$map['middle_name']] ?? '') ?: null : null,
                'last_name'        => $last ?: '—',
                'course'           => isset($map['course']) ? trim($row[$map['course']] ?? '') ?: null : null,
                'year_level'       => isset($map['year_level']) ? trim($row[$map['year_level']] ?? '') ?: null : null,
                'scholarship_name' => (isset($map['scholarship_name']) && trim($row[$map['scholarship_name']] ?? '') !== '') ? trim($row[$map['scholarship_name']]) : 'Untitled Scholarship',
                'scholarship_type' => $this->matchType(isset($map['scholarship_type']) ? ($row[$map['scholarship_type']] ?? '') : ''),
                'status'           => $this->matchStatus(isset($map['status']) ? ($row[$map['status']] ?? '') : ''),
                'remarks'          => null,
                'source'           => 'Excel Import',
            ]);
            $imported++;
        }

        return response()->json([
            'message' => "Import finished — {$imported} scholars added".($skipped ? ", {$skipped} empty rows skipped" : '').'.',
            'reload'  => true,
        ]);
    }

    /** Import the bundled external scholar list (mock data from partner agencies). */
    public function importExternal(Request $r) {
        $existing = Scholar::where('source','External Import')->count();
        if ($existing > 0) {
            return response()->json(['message'=>'The external list has already been imported ('.$existing.' scholars).'], 422);
        }

        $count = 0;
        foreach ($this->externalMockScholars() as $m) {
            Scholar::create($m + ['source'=>'External Import','status'=>'Active',
                'scholarship_type'=>'External','enrollment_status'=>'Enrolled',
                'requirements_met'=>true,'graduation_status'=>'On Track']);
            $count++;
        }

        return response()->json(['message'=>"Imported {$count} scholars from the external list.",'reload'=>true]);
    }

    // ── helpers ──────────────────────────────────────────────────────────────

    private function readCsv(string $path): array {
        $rows = [];
        if (($h = fopen($path, 'r')) !== false) {
            while (($data = fgetcsv($h)) !== false) { $rows[] = $data; }
            fclose($h);
        }
        return $rows;
    }

    /** Minimal .xlsx reader using ZipArchive + SimpleXML (no external packages). */
    private function readXlsx(string $path): array {
        if (!class_exists('ZipArchive')) {
            throw new \RuntimeException('ZipArchive is not available on this server.');
        }
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) return [];

        $shared = [];
        if (($ss = $zip->getFromName('xl/sharedStrings.xml')) !== false) {
            $xml = simplexml_load_string($ss);
            foreach ($xml->si ?? [] as $si) {
                $text = '';
                foreach ($si->t ?? [] as $t) { $text .= (string)$t; }
                if ((string)$si->t !== '') $text = (string)$si->t;
                $shared[] = $text;
            }
        }

        $sheet = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        if ($sheet === false) return [];

        $xml = simplexml_load_string($sheet);
        $rows = [];
        foreach ($xml->sheetData->row ?? [] as $row) {
            $cells = [];
            foreach ($row->c ?? [] as $c) {
                $ref = (string)$c['r'];
                $col = preg_replace('/[0-9]/', '', $ref);
                $idx = $this->colIndex($col);
                $v = (string)($c->v ?? '');
                if ((string)$c['t'] === 's') { $v = $shared[(int)$v] ?? ''; }
                elseif ((string)$c['t'] === 'inlineStr' && isset($c->is->t)) { $v = (string)$c->is->t; }
                $cells[$idx] = $v;
            }
            $rows[] = $cells;
        }
        return $rows;
    }

    private function colIndex(string $col): int {
        $idx = 0;
        for ($i = 0, $l = strlen($col); $i < $l; $i++) {
            $idx = $idx * 26 + (ord(strtoupper($col[$i])) - 64);
        }
        return max(0, $idx - 1);
    }

    /** Match free-form header labels to scholar fields. */
    private function headerMap(array $header): array {
        $map = [];
        foreach ($header as $i => $label) {
            $h = strtolower(preg_replace('/[^a-z0-9]/i', '', (string)$label));
            if ($h === '') continue;
            if (in_array($h, ['firstname','givenname','fname']) || str_contains($h,'firstname')) $map['first_name'] = $i;
            elseif (in_array($h, ['lastname','surname','familyname','lname']) || str_contains($h,'lastname')) $map['last_name'] = $i;
            elseif (str_contains($h,'middlename') || $h === 'mname') $map['middle_name'] = $i;
            elseif (str_contains($h,'studentnumber') || str_contains($h,'studentid') || $h === 'edp' || str_contains($h,'edpno')) $map['student_number'] = $i;
            elseif (str_contains($h,'course') || str_contains($h,'program')) $map['course'] = $i;
            elseif (str_contains($h,'year')) $map['year_level'] = $i;
            elseif (str_contains($h,'scholarship') || str_contains($h,'grant') || str_contains($h,'programname')) { if (!isset($map['scholarship_name'])) $map['scholarship_name'] = $i; }
            elseif (str_contains($h,'type')) $map['scholarship_type'] = $i;
            elseif (str_contains($h,'status')) $map['status'] = $i;
        }
        return $map;
    }

    private function matchType(?string $v): string {
        return stripos(trim((string)$v), 'ext') === 0 ? 'External' : (stripos(trim((string)$v),'int') === 0 ? 'Internal' : 'External');
    }

    private function matchStatus(?string $v): string {
        $v = strtolower(trim((string)$v));
        if (str_contains($v,'grad')) return 'Graduated';
        if (str_contains($v,'inact')) return 'Inactive';
        return 'Active';
    }

    /** 50 mock scholars representing an external partner-agency list. */
    private function externalMockScholars(): array {
        $first = ['Maria Jose','Juan Miguel','Angelica','Joshua','Catherine','Mark Anthony','Jhon Lloyd','Krizza','Dennis','Shaira','Rico','Lovely','Ferdinand','Gracia','Kevin','Aileen','Rolando','Divina','Christian','Maricel','Elmer','Jasmine','Rogelio','Blessie','Arnel','Christine','Gilbert','Rowena','Alfredo','Melinda','Jayson','Karen','Vicente','Lorna','Reneboy','Analyn','Efren','Gemmalyn','Nestor','Precious','Jerome','Hazel','Danilo','Michelle','Ricardo','Jamaica','Armando','Cherry Mae','Bryan','Nenita'];
        $last  = ['Dela Cruz','Bautista','Villanueva','Mendoza','Santos','Reyes','Garcia','Lim','Aquino','Alonzo','Castillo','Fernandez','Salvador','Navarro','Tolentino','Mercado','Ramos','Pascual','Domingo','Cabrera','Ocampo','Velasco','Gutierrez','Sarmiento','Padilla','Soriano','Aguilar','Del Rosario','Torres','Manalo','Bagtas','Coronado','Enriquez','Fuentes','Galvez','Herrera','Ibanez','Jimenez','Lumbo','Marquez','Nepomuceno','Panganiban','Quintos','Robledo','Sicat','Tuazon','Umali','Vergara','Ybanez','Zamora'];
        $middle = ['A.','B.','C.','D.','E.','F.','G.','H.','J.','K.','L.','M.','N.','P.','R.','S.','T.','V.','W.','Y.'];
        $courses = ['BS Information Technology','BS Education','BS Nursing','BS Business Administration','BS Criminology','BS Accountancy','BS Elementary Education','BS Hospitality Management','BS Computer Science','BS Psychology'];
        $years = ['1st Year','2nd Year','3rd Year','4th Year'];
        $scholarships = [
            'CHED Merit Scholarship','DOST-SEI Undergraduate Scholarship','UniFAST Tertiary Education Subsidy',
            'PVAO Educational Benefits','SM Foundation College Scholarship','Metrobank Foundation Scholarship',
            'Ayala Foundation Scholarship','JG Summit Scholarship','DSWD Educational Assistance','GSIS Scholarship',
        ];
        $gwabases = [1.25, 1.45, 1.60, 1.75, 1.85];

        $rows = [];
        for ($i = 0; $i < 50; $i++) {
            $gwa = round($gwabases[$i % count($gwabases)] + (($i * 7) % 10) / 100, 2);
            $rows[] = [
                'student_number'   => 'scc2025-'.str_pad(60001 + $i, 6, '0', STR_PAD_LEFT),
                'first_name'       => $first[$i],
                'middle_name'      => $middle[($i * 3) % count($middle)],
                'last_name'        => $last[$i],
                'course'           => $courses[$i % count($courses)],
                'year_level'       => $years[$i % count($years)],
                'scholarship_name' => $scholarships[$i % count($scholarships)],
                'current_gwa'      => min($gwa, 2.0),
                'remarks'          => null,
            ];
        }
        return $rows;
    }
}
