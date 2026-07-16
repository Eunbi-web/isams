<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{ScholarshipApplication, Scholarship};
use Illuminate\Http\Request;

class AiController extends Controller {

    public function index(Request $request) {
        $query = ScholarshipApplication::with(['student.user','scholarship'])
            ->whereNotNull('ai_score');

        if ($request->filled('eligibility'))    $query->where('ai_eligibility', $request->eligibility);
        if ($request->filled('scholarship_id')) $query->where('scholarship_id', $request->scholarship_id);
        if ($request->filled('min_score'))      $query->where('ai_score', '>=', $request->min_score);
        if ($request->filled('status'))         $query->where('status', $request->status);

        $applications = $query->orderByDesc('ai_score')->paginate(20);
        $scholarships = Scholarship::where('status','Active')->get();

        $all   = ScholarshipApplication::whereNotNull('ai_score');
        $stats = [
            'total'       => (clone $all)->count(),
            'eligible'    => (clone $all)->where('ai_eligibility','Eligible')->count(),
            'review'      => (clone $all)->where('ai_eligibility','For Review')->count(),
            'not_eligible'=> (clone $all)->where('ai_eligibility','Not Eligible')->count(),
            'avg_score'   => round((clone $all)->avg('ai_score') ?? 0, 1),
        ];

        return view('admin.ai.index', compact('applications','scholarships','stats'));
    }

    /**
     * Core AI evaluation — called by ApplicationController and EligibilityController.
     * Returns score, eligibility, tag, reasoning.
     */
    public function evaluate($application): array {
        $sch      = $application->scholarship;
        $criteria = is_array($sch->ai_criteria)
            ? $sch->ai_criteria
            : json_decode($sch->ai_criteria ?? '{}', true);

        $gwaMax     = (float) ($criteria['gwa_max']    ?? 1.75);
        $incomeMax  = (int)   ($criteria['income_max'] ?? 400000);
        $noFailing  = (bool)  ($criteria['no_failing'] ?? true);
        $noDisc     = (bool)  ($criteria['no_discipline'] ?? false);

        $score  = 0;
        $issues = [];
        $tags   = [];

        // GWA — 40 pts
        $gwa = (float) ($application->gwa ?? 5.0);
        if ($gwa <= $gwaMax) {
            $gwaScore = round(40 * ($gwaMax / max($gwa, 0.01)));
            $score += min(40, $gwaScore);
            if ($gwa <= 1.25) { $score += 5; $tags[] = 'Excellence GWA'; }
        } else {
            $issues[] = "GWA {$gwa} exceeds required {$gwaMax}";
        }

        // Enrollment — 20 pts
        if (strtolower($application->enrollment_type ?? '') === 'regular') {
            $score += 20;
        } else {
            $score += 10;
            $issues[] = 'Irregular enrollment (-10 pts)';
        }

        // No failing — 20 pts
        if ($noFailing) {
            if (!$application->has_failing) {
                $score += 20;
            } else {
                $issues[] = 'Has failing grades this semester';
            }
        } else {
            $score += 20;
        }

        // Income — 15 pts
        $bracket = $application->income_bracket ?? '200_400';
        if ($bracket === 'below_200') {
            $score += 15;
        } elseif ($bracket === '200_400') {
            $score += ($incomeMax >= 400000) ? 10 : 5;
        } else {
            $issues[] = 'Income exceeds scholarship limit';
        }

        // Discipline — 10 pts
        if ($noDisc) {
            if (!$application->has_discipline) {
                $score += 10;
            } else {
                $issues[] = 'Has active disciplinary case';
            }
        } else {
            $score += 10;
        }

        $score = min(100, max(0, $score));

        $eligibility = $score >= 75 ? 'Eligible'
                     : ($score >= 50 ? 'For Review' : 'Not Eligible');

        if ($score >= 90)        $tag = 'Renewal Ready';
        elseif (!empty($issues)) $tag = 'Needs Requirements';
        else                     $tag = '';

        $reasoning = count($issues)
            ? 'Issues: ' . implode('; ', $issues)
            : 'Meets all core criteria for this scholarship.';

        return compact('score','eligibility','tag','reasoning');
    }
}
