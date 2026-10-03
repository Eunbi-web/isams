<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Scholar;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DsaReportController extends Controller {
    /** Active scholars report — printable and exportable (DSA portal only). */
    public function index(Request $r) {
        $scholars = Scholar::query()
            ->when($r->type, fn($q,$v)=>$q->where('scholarship_type',$v))
            ->orderBy('scholarship_type')
            ->orderBy('last_name')
            ->get();

        $active = $scholars->where('status','Active');

        return view('admin.dsa-report.index', [
            'scholars' => $scholars,
            'active'   => $active,
            'internal' => $active->where('scholarship_type','Internal')->count(),
            'external' => $active->where('scholarship_type','External')->count(),
            'generatedAt' => now(),
        ]);
    }

    /** CSV export of the active scholars list. */
    public function export(): StreamedResponse {
        $filename = 'active-scholars-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['No.','Student Number','Full Name','Course','Year Level','Scholarship','Type','Current GWA','Enrollment Status','Requirements Met','Graduation Status','Remarks']);
            $n = 1;
            Scholar::where('status','Active')->orderBy('scholarship_type')->orderBy('last_name')
                ->chunk(200, function ($rows) use (&$n, $out) {
                    foreach ($rows as $s) {
                        fputcsv($out, [
                            $n++, $s->student_number, $s->full_name, $s->course, $s->year_level,
                            $s->scholarship_name, $s->scholarship_type,
                            $s->current_gwa, $s->enrollment_status,
                            $s->requirements_met ? 'Yes' : 'No', $s->graduation_status, $s->remarks,
                        ]);
                    }
                });
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
