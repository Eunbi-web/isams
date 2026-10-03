<?php
namespace App\Services;

use App\Models\AiReport;
use App\Models\CounselingSession;
use App\Models\Scholar;
use App\Models\Scholarship;
use App\Models\ScholarshipApplication;
use App\Models\ScholarshipUpdate;
use Illuminate\Support\Facades\Http;

/**
 * AI-assisted narrative / summative report generator (Groq).
 *
 * Assembles scholarship monitoring and/or guidance counseling data from the
 * database, has Groq write a formal narrative document, and persists the
 * sanitized HTML as an AiReport so it can be re-opened, printed or exported.
 *
 * The "List of Active Scholars" table (Section 9) is NOT written by the AI —
 * it is rendered here from live data and spliced into the document so the
 * table forwarded to Accounting is complete and numerically exact.
 */
class ComprehensiveReportService {
    /** Section 9 placeholder the model must emit so the real table can be spliced in. */
    private const TABLE_MARKER = '<!--SCHOLAR_TABLE-->';

    /**
     * @param array{scope:string, academic_year:string, semester:string, portal:string} $config
     *        scope: 'scholarship' | 'counseling' | 'both'
     */
    public function generate(array $config): AiReport {
        $scope         = in_array($config['scope'], ['scholarship','counseling','both'], true) ? $config['scope'] : 'both';
        $academicYear  = trim($config['academic_year']);
        $semester      = trim($config['semester']);
        $preparedBy    = auth()->user()->name ?? 'Student Affairs Office';
        $office        = ($config['portal'] ?? 'dsa') === 'scholarship' ? 'Scholarship Office' : 'Student Affairs Office';
        $today         = now()->format('F j, Y');

        $title = match ($scope) {
            'scholarship' => 'Scholarship Monitoring Narrative Report',
            'counseling'  => 'Guidance Counseling Narrative Report',
            default       => 'Comprehensive Scholarship and Counseling Report',
        };
        $title .= ' — A.Y. '.$academicYear.', '.$semester;

        [$prompt, $structureNote] = $this->buildPrompt($scope, $title, $academicYear, $semester, $preparedBy, $office, $today);

        $response = Http::timeout(180)
            ->withHeaders([
                'Authorization' => 'Bearer ' . config('services.groq.key', env('GROQ_API_KEY', '')),
                'Content-Type'  => 'application/json',
            ])
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'       => 'openai/gpt-oss-20b',
                'messages'    => [
                    ['role' => 'system', 'content' =>
                        'You write formal, ready-to-print narrative and summative reports for the Student Affairs Office of Saint Columban College, Pagadian City, Philippines. '
                        .'Output ONLY a clean HTML fragment using h2, h3, p, table, thead, tbody, tr, th, td, ul, ol, li, strong and em tags. '
                        .'Never use markdown, never use <html>, <head>, <body>, <style> or <script> tags. '
                        .'Write in formal Filipino academic English, third person, dignified and professional — the register of an official university report.'],
                    ['role' => 'user',   'content' => $prompt],
                ],
                'max_tokens'       => 8000,
                'temperature'      => 0.45,
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
        $html = preg_replace('/```html|```/i', '', $html);

        // Splice the deterministic active-scholars table where the model was
        // told to leave the marker (fall back to the end of the document).
        $table = $scope === 'counseling' ? '' : $this->buildScholarsTable();
        if ($table !== '') {
            if (str_contains($html, self::TABLE_MARKER)) {
                $html = str_replace(self::TABLE_MARKER, $table, $html);
            } else {
                $html .= $table;
            }
        } else {
            $html = str_replace(self::TABLE_MARKER, '', $html);
        }

        return AiReport::create([
            'portal'        => $config['portal'] ?? 'dsa',
            'scope'         => $scope,
            'title'         => $title,
            'academic_year' => $academicYear,
            'semester'      => $semester,
            'prepared_by'   => $preparedBy.' — '.$office,
            'content_html'  => $html,
        ]);
    }

    // ---------------------------------------------------------------- prompts

    private function buildPrompt(string $scope, string $title, string $ay, string $sem, string $preparedBy, string $office, string $today): array {
        $structure = $this->structureInstructions($scope);

        $prompt = "Write the complete formal report titled \"{$title}\" for {$office}. "
            ."It must be ready to print and forward to department heads.\n\n"
            ."OUTPUT FORMAT — a single clean HTML fragment in this order:\n"
            .$structure."\n\n"
            ."STYLE RULES:\n"
            ."- Formal Filipino academic tone, third person, no contractions.\n"
            ."- Use <h2> for the section headings, <p> for narrative paragraphs, <ul>/<ol> for lists, <strong> for key names, numbers, GWAs and dates.\n"
            ."- Ground EVERY claim in the DATA below. Do not invent names, numbers or facts; where data is absent, write generically.\n"
            ."- Use the pre-computed percentages exactly as given — do not recalculate.\n"
            ."- The closing section must certify the accuracy of the report and end with 'Prepared by: {$preparedBy}', '{$office}', and 'Date generated: {$today}'.\n\n"
            ."REPORT COVER INFORMATION:\n"
            ."- Report title: {$title}\n"
            ."- Academic year: {$ay}; Semester: {$sem}\n"
            ."- Date generated: {$today}\n"
            ."- Prepared by: {$preparedBy} ({$office})\n"
            ."- Institution: Saint Columban College, Pagadian City\n\n"
            .$this->dataBlocks($scope)
            ."\nNow write the complete report HTML.";

        return [$prompt, $structure];
    }

    private function structureInstructions(string $scope): string {
        if ($scope === 'both') {
            return "1. Cover information block (centered <h2> report title, then <p> lines for academic year and semester, date generated, prepared by, and institution).\n"
                ."2. <h2>Section 1 — Executive Summary</h2>: 2-3 paragraphs overviewing the whole reporting period for scholarship and counseling.\n"
                ."3. <h2>Section 2 — Scholarship Program Overview</h2>: narrative on active programs, their types (government, private, institutional) and notable updates.\n"
                ."4. <h2>Section 3 — Scholar Status Report</h2>: a formal paragraph per status category — maintaining scholars (count + percentage), at-risk scholars (names and GWA gaps), non-compliant scholars (with recommended actions), and graduating/graduated scholars.\n"
                ."5. <h2>Section 4 — Scholar Performance Analysis</h2>: patterns across the data — department/course with the highest retention, program with the most at-risk scholars, average GWA of active scholars.\n"
                ."6. <h2>Section 5 — Guidance Counseling Services Summary</h2>: total sessions, most common concerns, service utilization, notable observations.\n"
                ."7. <h2>Section 6 — Counseling Concern Analysis</h2>: formal observations of patterns (month-to-month movement, year levels seeking counseling most).\n"
                ."8. <h2>Section 7 — Recommendations</h2>: <ol> with 3 to 5 specific, actionable recommendations grounded in the data.\n"
                ."9. On its own line, insert the exact marker ".self::TABLE_MARKER." — a pre-formatted active-scholars table will be inserted there automatically; do NOT write a table yourself.\n"
                ."10. <h2>Section 8 — Closing Statement</h2>: formal certification of accuracy with the signature block described below.\n"
                ."Number the visible sections exactly as shown (Section 1, Section 2, ... Section 8).";
        }
        if ($scope === 'scholarship') {
            return "1. Cover information block (centered <h2> report title, then <p> lines for academic year and semester, date generated, prepared by, and institution).\n"
                ."2. <h2>Section 1 — Executive Summary</h2>: 2 paragraphs overviewing the scholarship monitoring picture for the period.\n"
                ."3. <h2>Section 2 — Scholarship Program Overview</h2>: narrative on active programs, their types and notable updates.\n"
                ."4. <h2>Section 3 — Scholar Status Report</h2>: a formal paragraph per status category — maintaining, at-risk (names and GWA gaps), non-compliant (with recommended actions), graduating/graduated.\n"
                ."5. <h2>Section 4 — Scholar Performance Analysis</h2>: retention by course, at-risk concentration by program, average GWA.\n"
                ."6. <h2>Section 5 — Recommendations</h2>: <ol> with 3 to 5 specific, actionable recommendations.\n"
                ."7. On its own line, insert the exact marker ".self::TABLE_MARKER." — a pre-formatted active-scholars table will be inserted there automatically; do NOT write a table yourself.\n"
                ."8. <h2>Section 6 — Closing Statement</h2>: formal certification of accuracy with the signature block described below.\n"
                ."Number the visible sections exactly as shown (Section 1, Section 2, ... Section 6).";
        }
        return "1. Cover information block (centered <h2> report title, then <p> lines for academic year and semester, date generated, prepared by, and institution).\n"
            ."2. <h2>Section 1 — Executive Summary</h2>: 2 paragraphs overviewing the guidance counseling picture for the period.\n"
            ."3. <h2>Section 2 — Counseling Services Summary</h2>: total sessions, most common concerns, utilization, notable observations.\n"
            ."4. <h2>Section 3 — Counseling Concern Analysis</h2>: formal observations of patterns (month-to-month movement, year levels seeking counseling most, priority mix).\n"
            ."5. <h2>Section 4 — Recommendations</h2>: <ol> with 3 to 5 specific, actionable recommendations grounded in the session data.\n"
            ."6. <h2>Section 5 — Closing Statement</h2>: formal certification of accuracy with the signature block described below.\n"
            ."Number the visible sections exactly as shown (Section 1, Section 2, ... Section 5).";
    }

    private function dataBlocks(string $scope): string {
        $blocks = '';
        if ($scope !== 'counseling') {
            $blocks .= $this->scholarshipDataBlock();
        }
        if ($scope !== 'scholarship') {
            $blocks .= $this->counselingDataBlock();
        }
        return $blocks;
    }

    // ------------------------------------------------------------ data blocks

    private function scholarshipDataBlock(): string {
        $scholars = Scholar::orderBy('last_name')->orderBy('first_name')->get();
        $active   = $scholars->where('status', 'Active')->values();
        $pct      = fn(int $n) => $active->count() ? round($n / $active->count() * 100, 1).'%' : '0%';

        $maintaining    = $active->filter(fn($s) => $s->is_maintaining);
        $atRisk         = $active->where('graduation_status', 'At Risk')->values();
        $notEnrolled    = $active->where('enrollment_status', 'Not Enrolled')->values();
        // Non-compliant: active + enrolled but failing to maintain requirements
        $nonCompliant   = $active->where('enrollment_status', 'Enrolled')->reject(fn($s) => $s->requirements_met)->values();
        $graduated      = $scholars->where('graduation_status', 'Graduated')->values();
        $graduating     = $active->filter(fn($s) => str_starts_with(trim((string)$s->year_level), '4'))->values();
        $gwaScholars    = $active->filter(fn($s) => $s->current_gwa !== null);
        $avgGwa         = $gwaScholars->count() ? round((float)$gwaScholars->avg('current_gwa'), 2) : null;

        // Required GWA ceiling per scholarship program, for stating GWA gaps
        $criteria = Scholarship::whereNotNull('ai_criteria')->get()
            ->mapWithKeys(fn($p) => [trim($p->name) => (float)($p->ai_criteria['gwa_max'] ?? 0)])
            ->filter();

        $block = "SCHOLARSHIP MONITORING DATA (as of ".now()->format('F j, Y')."):\n";
        $block .= "- Total scholars on record: {$scholars->count()}; active: {$active->count()}; inactive: {$scholars->where('status','Inactive')->count()}\n";
        $block .= "- Maintaining scholars (active + enrolled + requirements met): {$maintaining->count()} ({$pct($maintaining->count())} of active)\n";
        $block .= "- At-risk scholars: {$atRisk->count()} ({$pct($atRisk->count())} of active)\n";
        $block .= "- Non-compliant scholars (active, enrolled, NOT maintaining requirements): {$nonCompliant->count()} ({$pct($nonCompliant->count())} of active)\n";
        $block .= "- Active but not enrolled: {$notEnrolled->count()} ({$pct($notEnrolled->count())} of active)\n";
        $block .= "- Graduating (4th year, still active): {$graduating->count()}; graduated: {$graduated->count()}\n";
        $block .= '- Average GWA of active scholars with recorded GWA: '.($avgGwa ?? 'no GWA data').($avgGwa !== null ? " (across {$gwaScholars->count()} scholars)" : '')."\n";
        $block .= '- Scholars by scholarship source: Internal '.$active->where('scholarship_type','Internal')->count().'; External '.$active->where('scholarship_type','External')->count()."\n\n";

        if ($atRisk->count()) {
            $block .= "AT-RISK SCHOLARS (name — course/year — scholarship — GWA — gap):\n";
            foreach ($atRisk->take(30) as $s) {
                $ceiling = $criteria[trim($s->scholarship_name)] ?? null;
                $gap = ($ceiling && $s->current_gwa !== null)
                    ? sprintf('gap of %.2f above the %.2f ceiling for %s', (float)$s->current_gwa - $ceiling, $ceiling, $s->scholarship_name)
                    : 'ceiling not on record';
                $block .= '- '.$s->full_name.' — '.($s->course ?: 'course N/A').', '.($s->year_level ?: 'N/A').' — '.$s->scholarship_name.' — GWA '.($s->current_gwa ?? 'not recorded').' ('.$gap.")\n";
            }
            if ($atRisk->count() > 30) $block .= '- (and '.($atRisk->count() - 30)." more at-risk scholars)\n";
            $block .= "\n";
        }
        if ($nonCompliant->count()) {
            $block .= "NON-COMPLIANT SCHOLARS (active, enrolled, requirements NOT met — with monitoring remarks):\n";
            foreach ($nonCompliant->take(30) as $s) {
                $block .= '- '.$s->full_name.' — '.$s->scholarship_name.' — GWA '.($s->current_gwa ?? 'not recorded').($s->remarks ? ' — remarks: '.str_replace("\n", ' ', $s->remarks) : '')."\n";
            }
            if ($nonCompliant->count() > 30) $block .= '- (and '.($nonCompliant->count() - 30)." more)\n";
            $block .= "\n";
        }
        if ($notEnrolled->count()) {
            $block .= "ACTIVE SCHOLARS CURRENTLY NOT ENROLLED:\n";
            foreach ($notEnrolled->take(20) as $s) {
                $block .= '- '.$s->full_name.' — '.$s->scholarship_name.' — '.($s->course ?: 'course N/A')."\n";
            }
            if ($notEnrolled->count() > 20) $block .= '- (and '.($notEnrolled->count() - 20)." more)\n";
            $block .= "\n";
        }

        // Retention / concentration patterns by course and by program
        $byCourse = $active->groupBy(fn($s) => $s->course ?: 'Unknown')->map(fn($g) => [
            'total' => $g->count(),
            'maintaining' => $g->filter(fn($s) => $s->is_maintaining)->count(),
            'at_risk' => $g->where('graduation_status','At Risk')->count(),
        ])->sortByDesc(fn($v) => $v['total']);
        $block .= "ACTIVE SCHOLARS BY COURSE (course — active: n, maintaining: n, at-risk: n):\n";
        foreach ($byCourse->take(15) as $course => $v) {
            $block .= "- {$course} — active: {$v['total']}, maintaining: {$v['maintaining']}, at-risk: {$v['at_risk']}\n";
        }
        $byProgram = $active->groupBy(fn($s) => $s->scholarship_name ?: 'Unknown')->map(fn($g) => [
            'total' => $g->count(),
            'at_risk' => $g->where('graduation_status','At Risk')->count(),
            'non_compliant' => $g->where('enrollment_status','Enrolled')->reject(fn($s) => $s->requirements_met)->count(),
        ])->sortByDesc(fn($v) => $v['total']);
        $block .= "ACTIVE SCHOLARS BY SCHOLARSHIP PROGRAM (program — active: n, at-risk: n, non-compliant: n):\n";
        foreach ($byProgram->take(15) as $prog => $v) {
            $block .= "- {$prog} — active: {$v['total']}, at-risk: {$v['at_risk']}, non-compliant: {$v['non_compliant']}\n";
        }

        // Programs + applications + recent program updates
        $programs = Scholarship::get();
        $block .= "\nSCHOLARSHIP PROGRAM DATA:\n";
        $block .= "- Total scholarship programs on record: {$programs->count()}\n";
        if ($programs->count()) {
            $block .= '- Programs by type: '.$programs->groupBy(fn($p) => $p->type ?: 'Unclassified')
                ->map(fn($g, $t) => $t.': '.$g->count())->implode('; ')."\n";
            $block .= '- Programs by status: '.$programs->groupBy(fn($p) => $p->status ?: 'Unknown')
                ->map(fn($g, $st) => $st.': '.$g->count())->implode('; ')."\n";
        }
        $appCounts = ScholarshipApplication::selectRaw('status, count(*) as n')->groupBy('status')->pluck('n', 'status');
        if ($appCounts->count()) {
            $block .= '- Scholarship applications by status: '.$appCounts->map(fn($n, $st) => $st.': '.$n)->implode('; ')."\n";
        }
        $updates = ScholarshipUpdate::with('scholarship')->latest()->take(5)->get();
        if ($updates->count()) {
            $block .= "RECENT SCHOLARSHIP PROGRAM UPDATES:\n";
            foreach ($updates as $u) {
                $block .= '- '.($u->scholarship->name ?? 'General').': '.$u->title.' (posted '.$u->created_at->format('F j, Y').")\n";
            }
        }
        return $block;
    }

    private function counselingDataBlock(): string {
        $sessions = CounselingSession::with('student')->orderBy('session_date')->get();
        $completed = $sessions->where('status', 'Completed')->count();
        $scheduled = $sessions->where('status', 'Scheduled')->count();
        $inQueue   = $sessions->where('status', 'In Queue')->count();
        $served    = $sessions->pluck('student_id')->unique()->count();
        $followUps = $sessions->where('follow_up_required', true)->count();

        $block = "GUIDANCE COUNSELING DATA (as of ".now()->format('F j, Y')."):\n";
        $block .= "- Total counseling requests on record: {$sessions->count()}\n";
        $block .= "- Sessions completed: {$completed}; scheduled (upcoming): {$scheduled}; still in queue: {$inQueue}\n";
        $block .= "- Distinct students served: {$served}\n";
        $block .= "- Sessions flagged for follow-up: {$followUps}\n";

        if ($sessions->count()) {
            $block .= '- Concern types raised (most common first): '.$sessions->groupBy(fn($s) => $s->concern_type ?: 'Unspecified')
                ->map->count()->sortDesc()->map(fn($n, $t) => "{$t}: {$n}")->implode('; ')."\n";
            $byYear = $sessions->filter(fn($s) => $s->student && $s->student->year_level)
                ->groupBy(fn($s) => $s->student->year_level)->map->count()->sortDesc();
            if ($byYear->count()) {
                $block .= '- Sessions by student year level: '.$byYear->map(fn($n, $y) => "{$y}: {$n}")->implode('; ')."\n";
            }
            $block .= '- Priority mix: '.$sessions->groupBy(fn($s) => $s->priority ?: 'Normal')
                ->map->count()->sortDesc()->map(fn($n, $p) => "{$p}: {$n}")->implode('; ')."\n";

            // Month-by-month trend so the AI can compare periods
            $monthly = $sessions->filter(fn($s) => $s->session_date)
                ->groupBy(fn($s) => $s->session_date->format('Y-m'))
                ->map->count()->sortKeys()->take(-12);
            if ($monthly->count() > 1) {
                $block .= '- Sessions per month (chronological): '.$monthly->map(fn($n, $m) => $m.': '.$n)->implode('; ')."\n";
            }
            $completedSessions = $sessions->where('status', 'Completed')->filter(fn($s) => $s->notes);
            if ($completedSessions->count()) {
                $block .= "- Completed sessions with counselor notes available: {$completedSessions->count()}\n";
            }
        }
        return $block;
    }

    // ------------------------------------------------- deterministic table

    private function buildScholarsTable(): string {
        $rows = Scholar::where('status', 'Active')
            ->orderBy('last_name')->orderBy('first_name')->get();

        if (!$rows->count()) {
            return '<h2>List of Active Scholars</h2><p><em>No active scholars on record for this period.</em></p>';
        }

        $html = '<h2>List of Active Scholars</h2>'
            .'<p>The following table lists all currently active scholars as of '.now()->format('F j, Y').'. '
            .'A copy of this table is forwarded to the Accounting Office for the release of benefits.</p>'
            .'<table><thead><tr><th>Name</th><th>Program</th><th>Year Level</th><th>Scholarship Program</th><th>Current GWA</th><th>Status</th></tr></thead><tbody>';
        foreach ($rows as $s) {
            $status = $s->is_maintaining ? 'Maintaining'
                : ($s->graduation_status === 'At Risk' ? 'At Risk'
                : ($s->enrollment_status === 'Not Enrolled' ? 'Not Enrolled' : 'Not Maintaining'));
            $html .= '<tr>'
                .'<td>'.e($s->full_name).'</td>'
                .'<td>'.e($s->course ?: '—').'</td>'
                .'<td>'.e($s->year_level ?: '—').'</td>'
                .'<td>'.e($s->scholarship_name ?: '—').'</td>'
                .'<td>'.($s->current_gwa !== null ? number_format((float)$s->current_gwa, 2) : '—').'</td>'
                .'<td>'.e($status).'</td>'
                .'</tr>';
        }
        $html .= '</tbody></table>';
        return $html;
    }
}
