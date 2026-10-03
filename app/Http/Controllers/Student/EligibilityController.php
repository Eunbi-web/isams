<?php
namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use App\Models\EligibilityProfile;
use App\Http\Controllers\Admin\AiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EligibilityController extends Controller {

    // Common scholarship requirements the student inputs before running
    // the Eligibility Test (step 1 of the page flow).
    public const YEAR_LEVELS = ['1st Year','2nd Year','3rd Year','4th Year','5th Year'];
    public const ENROLLMENT_TYPES = ['Regular','Irregular'];
    public const INCOME_BRACKETS = [
        'below_200' => 'Below ₱200,000',
        '200_400'   => '₱200,000 – ₱400,000',
        'above_400' => 'Above ₱400,000',
    ];
    public const ACADEMIC_HONORS = ['None','With Honors','With High Honors','With Highest Honors'];

    public function index() {
        $user    = auth()->user();
        $student = $user->student;

        // Step 1 — the student must input their data first. Until a profile
        // exists, the page shows only the data form (no test results yet).
        $profile = $student
            ? EligibilityProfile::where('student_id', $student->id)->first()
            : null;

        $scholarships = collect();
        $eligibilityMap  = [];
        $overallScore    = 0;
        $overallEligibility = 'Not Eligible';

        if ($profile) {
            $scholarships = Scholarship::with(['applications' => function($q) use ($student) {
                if ($student) $q->where('student_id', $student->id);
            }])->where('status','Active')->latest()->get();

            $aiCtrl = new AiController();

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
                    // Run AI evaluation using the student's test data
                    $mockApp = new \App\Models\ScholarshipApplication([
                        'enrollment_type' => $profile->enrollment_type,
                        'has_failing'     => $profile->has_failing,
                        'has_discipline'  => $profile->has_discipline,
                        'income_bracket'  => $profile->income_bracket,
                    ]);
                    $mockApp->scholarship = $sch;

                    $result = $aiCtrl->evaluate($mockApp, $profile);
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
        }

        return view('student.eligibility.index', compact(
            'scholarships','eligibilityMap','overallScore',
            'overallEligibility','student','profile'
        ));
    }

    /**
     * Step 1/3 — save (or update) the student's eligibility test data,
     * then re-run the test. The saved row powers the Edit function:
     * the form is pre-filled from it on every visit.
     *
     * GWA is deliberately NOT collected here (students could mistype or
     * misstate it) — it is verified physically at the SAO. Documents
     * (School ID photo, Certificate of Enrollment) arrive as compressed
     * base64 data URLs from the page JS and are stored in the DB so they
     * persist on Vercel's ephemeral filesystem.
     */
    public function saveProfile(Request $request) {
        $user    = auth()->user();
        $student = $user->student;

        if (!$student) {
            return back()->with('error', 'No student record is linked to your account.');
        }

        $existing = EligibilityProfile::where('student_id', $student->id)->first();

        $data = $request->validate([
            'year_level'      => ['required','in:'.implode(',', self::YEAR_LEVELS)],
            'enrollment_type' => ['required','in:'.implode(',', self::ENROLLMENT_TYPES)],
            'family_income'   => ['required','numeric','min:0','max:99999999'],
            'academic_honors' => ['required','in:'.implode(',', self::ACADEMIC_HONORS)],
            'has_failing'     => ['required','in:0,1'],
            'has_discipline'  => ['required','in:0,1'],
            'school_id_data'  => [$existing?->school_id_photo ? 'nullable' : 'required','string','max:2800000'],
            'coe_data'        => [$existing?->coe_file       ? 'nullable' : 'required','string','max:2800000'],
        ], [
            'school_id_data.required' => 'Please upload or take a photo of your School ID.',
            'coe_data.required'       => 'Please upload your Certificate of Enrollment.',
            'school_id_data.max'      => 'The School ID photo is too large (max ~2 MB).',
            'coe_data.max'            => 'The Certificate of Enrollment file is too large (max ~2 MB).',
        ]);

        $imageTypes  = ['data:image/jpeg;base64,', 'data:image/png;base64,', 'data:image/webp;base64,'];
        $allowedMime = $imageTypes;
        foreach (['school_id_data' => 'school_id_photo', 'coe_data' => 'coe_file'] as $field => $column) {
            if (!empty($data[$field])) {
                if ($field === 'coe_data') {
                    $allowedMime = array_merge($imageTypes, ['data:application/pdf;base64,']);
                }
                $payload = substr($data[$field], strpos($data[$field], ',') + 1);
                $valid = str_starts_with($data[$field], 'data:')
                    && in_array(substr($data[$field], 0, strpos($data[$field], ',') + 1), $allowedMime, true)
                    && base64_decode($payload, true) !== false;
                if (!$valid) {
                    return back()->withInput()->with('error', 'Invalid file format. Please upload a photo'.($field === 'coe_data' ? ' or PDF' : '').' of your document.');
                }
            }
        }

        // Derive the income bracket used by the AI scoring from the actual
        // family income the student typed in.
        $income = (float) $data['family_income'];
        $bracket = $income <= 200000 ? 'below_200' : ($income <= 400000 ? '200_400' : 'above_400');

        EligibilityProfile::updateOrCreate(
            ['student_id' => $student->id],
            [
                'year_level'      => $data['year_level'],
                'enrollment_type' => $data['enrollment_type'],
                'family_income'   => $income,
                'income_bracket'  => $bracket,
                'academic_honors' => $data['academic_honors'],
                'has_failing'     => (bool) $data['has_failing'],
                'has_discipline'  => (bool) $data['has_discipline'],
                'school_id_photo' => $data['school_id_data'] ?? $existing?->school_id_photo,
                'coe_file'        => $data['coe_data']       ?? $existing?->coe_file,
            ]
        );

        return redirect()
            ->route('student.eligibility')
            ->with('success', 'Your data has been saved and the Eligibility Test has been run.');
    }

    /**
     * Server-side Groq generation for the Eligibility Test page
     * (gap analysis / action plan). The API key never
     * reaches the browser — the page calls this endpoint from JS.
     */
    public function aiGenerate(Request $request) {
        $data = $request->validate([
            'action' => 'required|in:passport,gap,plan',
        ]);

        $user    = auth()->user();
        $student = $user->student;
        $profile = $student ? EligibilityProfile::where('student_id', $student->id)->first() : null;
        $aiCtrl  = new AiController();

        if (!$profile) {
            return response()->json([
                'message' => 'Please fill in your eligibility test data first.',
            ], 422);
        }

        $scholarships = Scholarship::where('status','Active')->latest()->get();

        $lines = [];
        $best  = 0;
        $bestElig = 'Not Eligible';
        foreach ($scholarships as $i => $sch) {
            $mockApp = new \App\Models\ScholarshipApplication([
                'enrollment_type' => $profile->enrollment_type,
                'has_failing'     => $profile->has_failing,
                'has_discipline'  => $profile->has_discipline,
                'income_bracket'  => $profile->income_bracket,
            ]);
            $mockApp->scholarship = $sch;
            $r = $aiCtrl->evaluate($mockApp, $profile);

            if ($r['score'] > $best) { $best = $r['score']; $bestElig = $r['eligibility']; }

            $lines[] = ($i+1).'. '.$sch->name.' ('.($sch->type ?? '').')'
                .' | Score:'.$r['score'].'%'
                .' | '.$r['eligibility']
                .($r['tag'] ? ' | '.$r['tag'] : '')
                .' | '.\Illuminate\Support\Str::limit($r['reasoning'], 80);
        }

        $nl = "\n";

        $profileText = 'STUDENT PROFILE:'.$nl
            .'Name: '.($user->name ?? 'Student').$nl
            .'Course: '.($student?->course ?? 'N/A').' '.($profile->year_level ?? '').$nl
            .'Enrollment: '.($profile->enrollment_type ?? 'N/A').$nl
            .'Academic Honors: '.($profile->academic_honors ?? 'None').$nl
            .'Annual Family Income: ₱'.number_format((float)($profile->family_income ?? 0)).$nl
            .'Best AI Score: '.$best.'% ('.$bestElig.')'.$nl;

        $schList = 'SCHOLARSHIP SCORES:'.$nl.implode($nl, $lines).$nl.$nl;

        $base = 'You are ISAMS AI at Saint Columban College, Pagadian City, Philippines. ';

        $prompts = [
            'passport' => $base.'Generate a formal SCHOLARSHIP PASSPORT document for this student.'.$nl.$nl
                .$profileText.$schList
                .'Generate a formal scholarship passport with these sections:'.$nl
                .'1. ELIGIBILITY SUMMARY: Overall status and best matches in 2 sentences.'.$nl
                .'2. TOP RECOMMENDED SCHOLARSHIPS: List the top 1-3 with score and why they qualify. Say "Visit SAO Office to apply physically."'.$nl
                ."3. GAP ANALYSIS: For each scholarship they don't fully qualify for, what exactly is missing (e.g., \"Need GWA of 1.75, currently 1.90 - need 0.15 improvement\").".$nl
                .'4. DOCUMENTS TO PREPARE: List standard physical application documents needed.'.$nl
                .'5. NEXT STEPS: 3 concrete actions for this semester.'.$nl.$nl
                .'Write formally. Plain text only. No asterisks or hashtags. This will be printed.',
            'gap' => $base.'Generate a detailed GAP ANALYSIS for this student.'.$nl.$nl
                .'STUDENT: '.($user->name ?? 'Student').' | Annual Income:₱'.number_format((float)($profile->family_income ?? 0))
                .' | '.($profile->enrollment_type ?? 'N/A').$nl.$nl
                .$schList
                .'For each scholarship, provide:'.$nl
                .'- Current score vs needed score (75% to be eligible)'.$nl
                .'- Exact gap in points'.$nl
                .'- Specific actions to close the gap'.$nl
                .'- Timeline estimate'.$nl.$nl
                .'Be specific with numbers. Plain text only. No asterisks, no markdown, no tables. Helpful and encouraging tone.',
            'plan' => $base.'Generate a PRIORITY ACTION PLAN for this student to maximize scholarship eligibility.'.$nl.$nl
                .'STUDENT: '.($user->name ?? 'Student').' | Annual Income:₱'.number_format((float)($profile->family_income ?? 0)).' | Best Score:'.$best.'%'.$nl.$nl
                .$schList
                .'Create a numbered action plan with:'.$nl
                .'1. Immediate actions (this week) - e.g., visit SAO for forms, upload School ID and Certificate of Enrollment'.$nl
                .'2. Short-term actions (this semester) - e.g., academic improvements needed'.$nl
                .'3. Documents to prepare before applying physically at SAO'.$nl
                .'4. Which scholarship to prioritize applying for first and why'.$nl.$nl
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
