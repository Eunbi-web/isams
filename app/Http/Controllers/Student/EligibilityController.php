<?php
namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use App\Http\Controllers\Admin\AiController;

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
}
