<?php
namespace App\Http\Controllers\Counselor;
use App\Http\Controllers\Controller;
use App\Models\CounselingSession;
use App\Models\Scholar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NarrativeReportController extends Controller {
    /**
     * Generate a formal narrative report for a counseling session with Groq AI.
     * Combines the session summary with the student's scholarship monitoring data.
     */
    public function generate(Request $r, CounselingSession $counseling) {
        $counseling->load('student');
        $student = $counseling->student;

        $sessionLines = [
            'Student: '.($student->full_name ?? 'Unknown'),
            'Course/Year: '.trim(($student->course ?? '—').' '.($student->year_level ?? '')),
            'Concern type: '.$counseling->concern_type,
            'Priority: '.$counseling->priority,
            'Preferred date: '.($counseling->preferred_date?->format('F j, Y') ?? 'None'),
            'Scheduled: '.(($counseling->session_date?->format('F j, Y') ?? '').' '.($counseling->session_time ?? '')),
            'Venue: '.($counseling->venue ?? '—'),
            'Status: '.$counseling->status,
            'Counselor notes / session summary: '.($counseling->notes ?: ($counseling->concern_detail ?: 'No detailed notes recorded.')),
        ];

        // Scholarship monitoring data, when the student is on the scholar list
        $monitoringLines = null;
        if ($student) {
            $scholar = Scholar::where('student_number', $student->student_id)
                ->orWhere(fn($q) => $q->where('first_name', $student->first_name)->where('last_name', $student->last_name))
                ->first();
            if ($scholar) {
                $monitoringLines = [
                    'Scholarship: '.$scholar->scholarship_name.' ('.$scholar->scholarship_type.')',
                    'Scholar status: '.$scholar->status,
                    'Current GWA: '.($scholar->current_gwa ?? 'not recorded'),
                    'Enrollment status: '.$scholar->enrollment_status,
                    'Maintaining scholarship requirements: '.($scholar->requirements_met ? 'Yes' : 'No'),
                    'Graduation status: '.$scholar->graduation_status,
                ];
            }
        }

        $prompt = "You are writing a formal Narrative Report for the Guidance Counseling Office of Saint Columban College, Pagadian City. "
            ."Write it in professional third-person report style, suitable for printing and filing in a student's guidance record.\n\n"
            ."Structure the report with these sections, each with a short heading: I. Header (report title, student name, course, date of session, counselor: ".(auth()->user()->name ?? 'Guidance Counselor')."); "
            ."II. Background of the Session; III. Summary of the Counseling Session; IV. Scholarship Monitoring Summary"
            .($monitoringLines ? " (use the monitoring data below)" : " (state that the student has no scholarship monitoring record on file)").
            "; V. Observations and Assessment; VI. Recommendations and Follow-up Plan. "
            ."Write fluent paragraphs — do not output bullet lists except in the header. Do not invent facts beyond the data provided; keep unknowns generic.\n\n"
            ."SESSION DATA:\n".implode("\n", $sessionLines)."\n\n"
            ."SCHOLARSHIP MONITORING DATA:\n".($monitoringLines ? implode("\n", $monitoringLines) : 'No scholar record found for this student.')."\n\n"
            ."Now write the complete narrative report.";

        try {
            $response = Http::timeout(60)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . config('services.groq.key', env('GROQ_API_KEY', '')),
                    'Content-Type'  => 'application/json',
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'            => 'openai/gpt-oss-20b',
                    'messages'         => [
                        ['role' => 'system', 'content' => 'You produce formal, well-structured narrative reports for a university guidance counseling office. Output plain text with clear section headings, no markdown symbols.'],
                        ['role' => 'user',   'content' => $prompt],
                    ],
                    'max_tokens'       => 1200,
                    'temperature'      => 0.4,
                    'reasoning_effort' => 'low',
                ]);

            $report = $response->ok()
                ? trim($response->json('choices.0.message.content', ''))
                : '';

            if ($report === '') {
                return response()->json(['message' => 'Groq AI did not return a report. Please try again.'], 502);
            }

            $counseling->update(['narrative_report' => $report]);

            return response()->json(['report' => $report, 'message' => 'Narrative report generated.']);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Could not reach the AI service. Please try again in a moment.'], 502);
        }
    }
}
