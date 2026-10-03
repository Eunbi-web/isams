<?php
namespace App\Services;

use App\Models\CounselingSession;
use App\Models\Scholar;
use Illuminate\Support\Facades\Http;

class NarrativeReportService {
    /**
     * Generate a detailed, formatted (HTML) narrative report for a counseling
     * session, combining session data with the student's scholarship monitoring
     * data. Returns a sanitized HTML fragment (no <html>/<body>/<style>).
     */
    public function generate(CounselingSession $counseling): string {
        $counseling->load('student');
        $student = $counseling->student;
        $counselor = auth()->user()->name ?? 'Guidance Counselor';

        $sessionData = [
            'Student name'          => $student->full_name ?? 'Unknown',
            'Student number'        => $student->student_id ?? '—',
            'Course and year level' => trim(($student->course ?? '—').' '.($student->year_level ?? '')),
            'Concern type'          => $counseling->concern_type,
            'Priority level'        => $counseling->priority,
            'Preferred date'        => $counseling->preferred_date?->format('F j, Y') ?? 'None indicated',
            'Scheduled date/time'   => trim(($counseling->session_date?->format('F j, Y') ?? '').' '.($counseling->session_time ?? '')) ?: 'Not yet scheduled',
            'Venue'                 => $counseling->venue ?? '—',
            'Session status'        => $counseling->status,
            'Follow-up required'    => $counseling->follow_up_required ? ('Yes — '.$counseling->follow_up_date?->format('F j, Y')) : 'No',
            'Queue position'        => $counseling->queue_position ? '#'.$counseling->queue_position : '—',
        ];

        // Student's stated concern
        $sessionData['Student concern description'] = $counseling->concern_detail ?: 'No description provided.';
        // Counselor's session summary (may be empty for uncompleted sessions)
        if ($counseling->notes) {
            $sessionData['Counselor session notes'] = $counseling->notes;
        }

        // Scholarship monitoring data, when the student is on the scholar list
        $monitoring = null;
        if ($student) {
            $monitoring = Scholar::where('student_number', $student->student_id)
                ->orWhere(fn($q) => $q->where('first_name', $student->first_name)->where('last_name', $student->last_name))
                ->first();
        }

        $monitoringBlock = $monitoring
            ? "The student HAS a scholarship monitoring record:\n"
              .'- Scholarship: '.$monitoring->scholarship_name.' ('.$monitoring->scholarship_type." source)\n"
              .'- Scholar status: '.$monitoring->status."\n"
              .'- Current GWA: '.($monitoring->current_gwa ?? 'not recorded')."\n"
              .'- Enrollment status: '.$monitoring->enrollment_status."\n"
              .'- Maintaining scholarship requirements: '.($monitoring->requirements_met ? 'Yes' : 'No')."\n"
              .'- Graduation status: '.$monitoring->graduation_status."\n"
              .'- Last monitored: '.($monitoring->last_monitored_at?->format('F j, Y') ?? 'never')
            : 'The student has NO scholarship monitoring record on the scholar list.';

        $infoRows = '';
        foreach ($sessionData as $k => $v) {
            $infoRows .= '- '.ucwords($k).': '.$v."\n";
        }

        $prompt = "Write a DETAILED, formal Narrative Report for the Guidance Counseling Office of Saint Columban College, Pagadian City, Philippines. "
            ."It will be printed and filed in the student's guidance record.\n\n"
            ."OUTPUT FORMAT — return ONLY a clean HTML fragment (no <html>, <head>, <body>, <style> or <script> tags, no markdown):\n"
            ."1. A centered title block: <h2>NARRATIVE REPORT</h2> followed by <p> lines for the office name, and the reference number REF-".str_pad((string)$counseling->id, 5, '0', STR_PAD_LEFT).'-'.now()->format('Y').".\n"
            ."2. A 'Session Information' data table (use <table class=\"nr-table\">) with one row per data field below.\n"
            ."3. Section II 'Background of the Session' — a substantial paragraph (4-6 sentences).\n"
            ."4. Section III 'Summary of the Counseling Session' — 2-3 detailed narrative paragraphs; use the counselor's session notes as the primary source and elaborate professionally without inventing facts.\n"
            ."5. Section IV 'Scholarship Monitoring Summary' — if monitoring data exists, render it as a second <table class=\"nr-table\"> (Scholarship, Source, Status, Current GWA, Enrollment, Requirements Met, Graduation Status) followed by one interpretive paragraph relating it to the counseling concern; if no record exists, one paragraph stating that and its implication.\n"
            ."6. Section V 'Observations and Assessment' — bullet list (<ul><li>) of at least 4 observations grounded in the data.\n"
            ."7. Section VI 'Recommendations and Follow-up Plan' — numbered list (<ol><li>) of at least 4 concrete, actionable recommendations.\n"
            ."8. A closing signature block: 'Prepared by:' with the counselor name and title 'Guidance Counselor', 'Noted by:' with 'Student Affairs Office', and a 'Date generated: ".now()->format('F j, Y')."' line.\n"
            ."Use <strong> for key facts (names, GWA, dates, statuses). Write in formal third person. Do NOT invent facts beyond the data given; keep unknowns generic.\n\n"
            ."SESSION DATA:\n".$infoRows."\n"
            ."SCHOLARSHIP MONITORING DATA:\n".$monitoringBlock."\n\n"
            ."Counselor of record: ".$counselor."\n"
            ."Report generated on: ".now()->format('F j, Y g:i A')."\n\n"
            ."Now write the complete report HTML.";

        $response = Http::timeout(90)
            ->withHeaders([
                'Authorization' => 'Bearer ' . config('services.groq.key', env('GROQ_API_KEY', '')),
                'Content-Type'  => 'application/json',
            ])
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'            => 'openai/gpt-oss-20b',
                'messages'         => [
                    ['role' => 'system', 'content' => 'You produce formal, detailed narrative reports for a university guidance counseling office as clean HTML fragments using h2/h3, p, table, ul, ol, li and strong tags only. Never use markdown. Never use script or style tags.'],
                    ['role' => 'user',   'content' => $prompt],
                ],
                'max_tokens'       => 3000,
                'temperature'      => 0.4,
                'reasoning_effort' => 'low',
            ]);

        $html = $response->ok()
            ? trim($response->json('choices.0.message.content', ''))
            : '';

        if ($html === '') {
            abort(502, 'Groq AI did not return a report. Please try again.');
        }

        // Basic hygiene: strip anything script-like the model may have emitted
        $html = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $html);
        $html = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
        $html = preg_replace('/<(style|iframe|object|embed)\b[^>]*>.*?<\/\1>/is', '', $html);

        $counseling->update(['narrative_report' => $html]);

        return $html;
    }
}
