<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\CounselingSession;
use App\Services\NarrativeReportService;
use Illuminate\Http\Request;

class NarrativeReportController extends Controller {
    public function __construct(private NarrativeReportService $reports) {}

    /** DSA portal: AI-assisted narrative report for a counseling session. */
    public function generate(Request $r, CounselingSession $counseling) {
        try {
            $html = $this->reports->generate($counseling);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => str_contains($e->getMessage(), 'Groq')
                    ? $e->getMessage()
                    : 'Could not reach the AI service. Please try again in a moment.',
            ], 502);
        }

        return response()->json(['report' => $html, 'message' => 'Narrative report generated.']);
    }
}
