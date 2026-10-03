<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\InteractsWithAiReports;
use App\Http\Controllers\Controller;
use App\Models\AiReport;
use App\Services\ComprehensiveReportService;
use Illuminate\Http\Request;

/**
 * ISAMS AI Comprehensive Report Generator — DSA portal.
 * One Groq-powered report combining Scholarship Monitoring, Scholar Status
 * Analysis and Guidance Counseling Summary into a single formal narrative
 * document, with a history so DSA staff can re-open past reports.
 */
class AiReportController extends Controller {
    use InteractsWithAiReports;

    public function index() {
        return view('admin.ai-report.index', [
            'reports'    => AiReport::where('portal', 'dsa')->latest()->take(10)->get(),
            'ayOptions'  => $this->ayOptions(),
            'currentAy'  => $this->ayOptions()[0],
            'currentSem' => $this->guessSemester(),
        ]);
    }

    public function generate(Request $r) {
        $data = $r->validate([
            'scope'         => 'required|in:scholarship,counseling,both',
            'academic_year' => 'required|string|max:20',
            'semester'      => 'required|string|max:30',
        ]);

        $report = app(ComprehensiveReportService::class)->generate([
            'scope'         => $data['scope'],
            'portal'        => 'dsa',
            'academic_year' => $data['academic_year'],
            'semester'      => $data['semester'],
        ]);

        return response()->json([
            'id'           => $report->id,
            'title'        => $report->title,
            'scope'        => $report->scope,
            'report'       => $report->content_html,
            'generated_at' => $report->created_at->format('M d, Y g:i A'),
            'message'      => 'AI comprehensive report generated.',
        ]);
    }

    /** Re-open a previously generated report into the preview panel. */
    public function view(AiReport $report) {
        abort_unless($report->portal === 'dsa', 404);
        return response()->json([
            'id'           => $report->id,
            'title'        => $report->title,
            'scope'        => $report->scope,
            'report'       => $report->content_html,
            'generated_at' => $report->created_at->format('M d, Y g:i A'),
        ]);
    }

    public function pdf(AiReport $report) {
        abort_unless($report->portal === 'dsa', 404);
        return $this->pdfResponse($report);
    }

    public function word(AiReport $report) {
        abort_unless($report->portal === 'dsa', 404);
        return $this->wordResponse($report);
    }
}
