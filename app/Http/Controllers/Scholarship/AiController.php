<?php
namespace App\Http\Controllers\Scholarship;
use App\Http\Controllers\Controller;
use App\Models\{ScholarshipApplication, Scholarship};
use Illuminate\Http\Request;

class AiController extends Controller {

    // CHANGE 1: complete status set for scholarship applications
    public const STATUSES = ['Pending','On Review','Approved','Rejected','Canceled'];

    public function index(Request $request) {
        // CHANGE 2: fetch ALL applications (evaluated or not), eager loading
        // student.user and scholarship. Previously only AI-evaluated rows were shown.
        $query = ScholarshipApplication::with(['student.user','scholarship']);

        // Existing filters (unchanged)
        if ($request->filled('eligibility'))    $query->where('ai_eligibility', $request->eligibility);
        if ($request->filled('scholarship_id')) $query->where('scholarship_id', $request->scholarship_id);
        if ($request->filled('min_score'))      $query->where('ai_score', '>=', $request->min_score);
        if ($request->filled('status')) {
            // 'Approved' also matches legacy 'Scholarship Granted' rows
            if ($request->status === 'Approved') {
                $query->whereIn('status', ['Approved','Scholarship Granted']);
            } else {
                $query->where('status', $request->status);
            }
        }

        // CHANGE 2 — new filters: date range + unevaluated only
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('created_at', '<=', $request->date_to);
        if ($request->input('unevaluated_only') == '1') {
            $query->where(function($q){ $q->whereNull('ai_run_at')->orWhere('ai_score', 0); });
        }

        // Ranked ordering: evaluated high scores first, unevaluated (ai_score 0/null)
        // at the bottom — unless the Applied Date header sort is active.
        $sort = $request->input('sort');
        if ($sort === 'date_asc') {
            $query->orderBy('created_at','asc');
        } elseif ($sort === 'date_desc') {
            $query->orderBy('created_at','desc');
        } else {
            $query->orderByRaw('COALESCE(ai_score,0) DESC')->orderBy('created_at','asc');
        }

        $applications = $query->paginate(20);
        $scholarships = Scholarship::where('status','Active')->get();

        // Existing evaluated stats (unchanged definition — but "evaluated" now
        // means actually scored: ai_run_at set AND ai_score > 0, since the table
        // also lists unevaluated rows whose ai_score column defaults to 0)
        $all   = ScholarshipApplication::whereNotNull('ai_run_at')->where('ai_score','>',0);
        $stats = [
            'total'       => (clone $all)->count(),
            'eligible'    => (clone $all)->where('ai_eligibility','Eligible')->count(),
            'review'      => (clone $all)->where('ai_eligibility','For Review')->count(),
            'not_eligible'=> (clone $all)->where('ai_eligibility','Not Eligible')->count(),
            'avg_score'   => round((clone $all)->avg('ai_score') ?? 0, 1),
        ];

        // CHANGE 2 — new application status counters
        $allApps          = ScholarshipApplication::query();
        $totalApplied     = (clone $allApps)->count();
        $pendingCount     = (clone $allApps)->where('status','Pending')->count();
        $onReviewCount    = (clone $allApps)->where('status','On Review')->count();
        $canceledCount    = (clone $allApps)->where('status','Canceled')->count();
        $completedCount   = (clone $allApps)->whereIn('status',['Approved','Scholarship Granted'])->count();
        $unevaluatedCount = (clone $allApps)->where(function($q){ $q->whereNull('ai_run_at')->orWhere('ai_score',0); })->count();

        // CHANGE 2 — pass filter values back for repopulating inputs
        $filters = $request->only(['eligibility','scholarship_id','min_score','status','date_from','date_to','unevaluated_only','sort']);

        return view('scholarship.ai.index', compact(
            'applications','scholarships','stats','filters',
            'totalApplied','pendingCount','onReviewCount','canceledCount','completedCount','unevaluatedCount'
        ));
    }

    // CHANGE 3: quick status change from the AI Filter table (AJAX or redirect)
    public function updateStatus(Request $request, ScholarshipApplication $application) {
        $data = $request->validate([
            'status' => 'required|in:Pending,On Review,Approved,Rejected,Canceled',
        ]);

        $application->update(['status' => $data['status']]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Status updated',
                'status'  => $data['status'],
            ]);
        }

        return back()->with('success', "Status updated to {$data['status']}!");
    }

    // CHANGE 6: single-application AI evaluation for the Evaluate button on
    // unevaluated rows (route previously aliased to index — now a real handler)
    public function runSingle(Request $request, ScholarshipApplication $application) {
        $result = $this->evaluate($application);

        $application->update([
            'ai_score'       => $result['score'],
            'ai_eligibility' => $result['eligibility'],
            'ai_tag'         => $result['tag'],
            'ai_reasoning'   => $result['reasoning'],
            'ai_run_at'      => now(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                    'message' => 'Evaluation complete',
                    'id'      => $application->id,
                    'status'  => $application->status,
                ] + $result);
        }

        return back()->with('success', "AI evaluation complete — Score: {$result['score']}% ({$result['eligibility']})");
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
