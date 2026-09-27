<?php
namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use App\Http\Controllers\Admin\AiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EligibilityController extends Controller {

    public function index() {
        $user    = auth()->user();
        $student = $user->student;

        $scholarships = Scholarship::with(['applications' => function($q) use ($student) {
            if ($student) $q->where('student_id', $student->id);
        }])->where('status','Active')->latest()->get();

        $eligibilityMap  = [];
        $overallScore    = 0;
        $overallEligibility = 'Not Eligible';
        $aiCtrl          = new AiController();

        foreach ($scholarships as $sch) {
            $existing = $student
                ? $sch->applications->where('student_id', $student->id)->first()
                : null;

            if ($existing && $existing->ai_score !== null) {
                $eligibilityMap[$sch->id] = [
                    'score'       => $existing->ai_score,
                    'eligibility' => $existing->ai_eligibility,
                    'tag'         => $existing->ai_tag,
                    'reasoning'   => $existing->ai_reasoning,
                    'applied'     => true,
                    'status'      => $existing->status,
                ];
            } else {
                // Run AI evaluation without saving
                $mockApp = new \App\Models\ScholarshipApplication([
                    'gwa'             => $student?->gwa ?? 2.5,
                    'enrollment_type' => $student?->enrollment_type ?? 'Regular',
                    'has_failing'     => false,
                    'has_discipline'  => false,
                    'income_bracket'  => $student?->income_bracket ?? '200_400',
                ]);
                $mockApp->scholarship = $sch;

                $result = $aiCtrl->evaluate($mockApp);
                $eligibilityMap[$sch->id] = [
                    'score'       => $result['score'],
                    'eligibility' => $result['eligibility'],
                    'tag'         => $result['tag'],
                    'reasoning'   => $result['reasoning'],
                    'applied'     => false,
                    'status'      => null,
                ];
            }

            if ($eligibilityMap[$sch->id]['score'] > $overallScore) {
                $overallScore       = $eligibilityMap[$sch->id]['score'];
                $overallEligibility = $eligibilityMap[$sch->id]['eligibility'];
            }
        }

        return view('student.eligibility.index', compact(
            'scholarships','eligibilityMap','overallScore',
            'overallEligibility','student'
        ));
    }

    /**
     * Server-side Groq generation for the AI Eligibility page
     * (passport / gap analysis / action plan). The API key never
     * reaches the browser — the old page called Groq from JS with a
     * model that has since been decommissioned.
     */
    public function aiGenerate(Request $request) {
        $data = $request->validate([
            'action' => 'required|in:passport,gap,plan',
        ]);

        $user    = auth()->user();
        $student = $user->student;
        $aiCtrl  = new AiController();

        $scholarships = Scholarship::where('status','Active')->latest()->get();

        $lines = [];
        $best  = 0;
        $bestElig = 'Not Eligible';
        foreach ($scholarships as $i => $sch) {
            $mockApp = new \App\Models\ScholarshipApplication([
                'gwa'             => $student?->gwa ?? 2.5,
                'enrollment_type' => $student?->enrollment_type ?? 'Regular',
                'has_failing'     => false,
                'has_discipline'  => false,
                'income_bracket'  => $student?->income_bracket ?? '200_400',
            ]);
            $mockApp->scholarship = $sch;
            $r = $aiCtrl->evaluate($mockApp);

            if ($r['score'] > $best) { $best = $r['score']; $bestElig = $r['eligibility']; }

            $lines[] = ($i+1).'. '.$sch->name.' ('.($sch->type ?? '').')'
                .' | Score:'.$r['score'].'%'
                .' | '.$r['eligibility']
                .($r['tag'] ? ' | '.$r['tag'] : '')
                .' | '.\Illuminate\Support\Str::limit($r['reasoning'], 80);
        }

        $bracketLabel = [
            'below_200' => 'Below 200,000 PHP',
            '200_400'   => '200,000 - 400,000 PHP',
            'above_400' => 'Above 400,000 PHP',
        ][$student?->income_bracket ?? ''] ?? ($student?->income_bracket ?? 'N/A');

        $nl = "\n";

        $profile = 'STUDENT PROFILE:'.$nl
            .'Name: '.($user->name ?? 'Student').$nl
            .'Course: '.($student?->course ?? 'N/A').' '.($student?->year_level ?? '').$nl
            .'GWA: '.number_format((float)($student?->gwa ?? 5.0), 2).$nl
            .'Enrollment: '.($student?->enrollment_type ?? 'N/A').$nl
            .'Income Bracket: '.$bracketLabel.$nl
            .'Best AI Score: '.$best.'% ('.$bestElig.')'.$nl;

        $schList = 'SCHOLARSHIP SCORES:'.$nl.implode($nl, $lines).$nl.$nl;

        $base = 'You are ISAMS AI at Saint Columban College, Pagadian City, Philippines. ';

        $prompts = [
            'passport' => $base.'Generate a formal SCHOLARSHIP PASSPORT document for this student.'.$nl.$nl
                .$profile.$schList
                .'Generate a formal scholarship passport with these sections:'.$nl
                .'1. ELIGIBILITY SUMMARY: Overall status and best matches in 2 sentences.'.$nl
                .'2. TOP RECOMMENDED SCHOLARSHIPS: List the top 1-3 with score and why they qualify. Say "Visit SAO Office to apply physically."'.$nl
                ."3. GAP ANALYSIS: For each scholarship they don't fully qualify for, what exactly is missing (e.g., \"Need GWA of 1.75, currently 1.90 - need 0.15 improvement\").".$nl
                .'4. DOCUMENTS TO PREPARE: List standard physical application documents needed.'.$nl
                .'5. NEXT STEPS: 3 concrete actions for this semester.'.$nl.$nl
                .'Write formally. Plain text only. No asterisks or hashtags. This will be printed.',
            'gap' => $base.'Generate a detailed GAP ANALYSIS for this student.'.$nl.$nl
                .'STUDENT: '.($user->name ?? 'Student').' | GWA:'.number_format((float)($student?->gwa ?? 5.0),2)
                .' | '.($student?->enrollment_type ?? 'N/A').' | Income:'.$bracketLabel.$nl.$nl
                .$schList
                .'For each scholarship, provide:'.$nl
                .'- Current score vs needed score (75% to be eligible)'.$nl
                .'- Exact gap in points'.$nl
                .'- Specific actions to close the gap (e.g., "Improve GWA by 0.10 to gain 8 more points")'.$nl
                .'- Timeline estimate (e.g., "Achievable next semester if GWA improves")'.$nl.$nl
                .'Be specific with numbers. Plain text only. No asterisks, no markdown, no tables. Helpful and encouraging tone.',
            'plan' => $base.'Generate a PRIORITY ACTION PLAN for this student to maximize scholarship eligibility.'.$nl.$nl
                .'STUDENT: '.($user->name ?? 'Student').' | GWA:'.number_format((float)($student?->gwa ?? 5.0),2)
                .' | '.($student?->enrollment_type ?? 'N/A').' | Income:'.$bracketLabel.' | Best Score:'.$best.'%'.$nl.$nl
                .$schList
                .'Create a numbered action plan with:'.$nl
                .'1. Immediate actions (this week) - e.g., visit SAO for forms, update profile'.$nl
                .'2. Short-term actions (this semester) - e.g., academic improvements needed'.$nl
                .'3. Documents to prepare before applying physically at SAO'.$nl
                .'4. Specific GWA target needed to unlock more scholarships'.$nl
                .'5. Which scholarship to prioritize applying for first and why'.$nl.$nl
                .'Note: All applications are done physically at the Student Affairs Office. Do not say apply online.'.$nl
                .'Be specific, encouraging, and practical. Plain text only. No asterisks, no markdown, no tables.',
        ];

        try {
            $res = Http::timeout(60)
                ->withHeaders([
                    'Authorization' => 'Bearer '.config('services.groq.key', env('GROQ_API_KEY', '')),
                    'Content-Type'  => 'application/json',
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'            => 'openai/gpt-oss-20b',
                    'messages'         => [['role' => 'user', 'content' => $prompts[$data['action']]]],
                    'max_tokens'       => 2000,
                    'temperature'      => 0.7,
                    'reasoning_effort' => 'low',
                ]);

            $text = $res->ok()
                ? trim($res->json('choices.0.message.content', ''))
                : '';

            if ($text !== '') {
                // gpt-oss likes markdown even when told not to use it; the
                // page renders plain pre-wrapped text, so strip decoration.
                $text = str_replace(['**', '__', '`'], '', $text);
                $text = preg_replace('/^\s*\|.*\|\s*$/m', '', $text); // drop table rows
                $text = preg_replace('/\n{3,}/', "\n\n", $text);
                $text = trim($text);
            }

            if ($text === '') {
                return response()->json([
                    'message' => 'AI returned an empty response. Please try again.',
                ], 502);
            }

            return response()->json(['text' => $text]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'AI service error: '.$e->getMessage(),
            ], 502);
        }
    }
}
