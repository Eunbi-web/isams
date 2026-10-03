<?php
namespace App\Http\Controllers\Scholarship;

use App\Http\Controllers\Concerns\InteractsWithAiReports;
use App\Http\Controllers\Controller;
use App\Models\AiReport;
use App\Services\ComprehensiveReportService;
use Illuminate\Http\Request;

/**
 * AI-assisted Narrative Report (Groq) — Scholarship Portal.
 * Replaces the former "AI Filter" page: generates a formal narrative report
 * from scholarship monitoring data (scholar statuses, GWA, enrollment,
 * requirements) and keeps a history of generated reports.
 */
class AiReportController extends Controller {
    use InteractsWithAiReports;

    public function index() {
        return view('scholarship.ai.index', [
            'reports'    => AiReport::where('portal', 'scholarship')->latest()->take(10)->get(),
            'ayOptions'  => $this->ayOptions(),
            'currentAy'  => $this->ayOptions()[0],
            'currentSem' => $this->guessSemester(),
        ]);
    }

    public function generate(Request $r) {
        $data = $r->validate([
            'academic_year' => 'required|string|max:20',
            'semester'      => 'required|string|max:30',
        ]);

        $report = app(ComprehensiveReportService::class)->generate([
            'scope'         => 'scholarship',
            'portal'        => 'scholarship',
            'academic_year' => $data['academic_year'],
            'semester'      => $data['semester'],
        ]);

        return response()->json([
            'id'           => $report->id,
            'title'        => $report->title,
            'report'       => $report->content_html,
            'generated_at' => $report->created_at->format('M d, Y g:i A'),
            'message'      => 'AI narrative report generated.',
        ]);
    }

    /** Re-open a previously generated report into the preview panel. */
    public function view(AiReport $report) {
        abort_unless($report->portal === 'scholarship', 404);
        return response()->json([
            'id'           => $report->id,
            'title'        => $report->title,
            'report'       => $report->content_html,
            'generated_at' => $report->created_at->format('M d, Y g:i A'),
        ]);
    }

    public function pdf(AiReport $report) {
        abort_unless($report->portal === 'scholarship', 404);
        return $this->pdfResponse($report);
    }

    public function word(AiReport $report) {
        abort_unless($report->portal === 'scholarship', 404);
        return $this->wordResponse($report);
    }
}
