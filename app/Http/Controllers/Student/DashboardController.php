<?php
namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;
use App\Models\{ScholarshipApplication,Scholarship,Announcement,EligibilityProfile};
use App\Http\Controllers\Admin\AiController;
class DashboardController extends Controller {
    public function index() {
        $student = auth()->user()->student;
        $myApplications = $student ? ScholarshipApplication::where('student_id',$student->id)->with('scholarship')->latest()->take(3)->get() : collect();
        $openScholarships = Scholarship::where('status','Active')->take(3)->get();
        $announcements = Announcement::latest()->take(3)->get();

        // Eligibility Test data saved by the student (from the Eligibility Test page)
        $profile = $student ? EligibilityProfile::where('student_id',$student->id)->first() : null;

        $aiScore = 0;
        $eligibility = 'Not Evaluated';
        $eligibleCount = 0;

        if ($profile) {
            // Exact score from the student's test data, evaluated against
            // every active scholarship (in-memory, single scholarships query)
            $aiCtrl = new AiController();
            $activeScholarships = Scholarship::where('status','Active')->get();
            foreach ($activeScholarships as $sch) {
                $mockApp = new ScholarshipApplication([
                    'gwa'             => $profile->gwa ?? 2.5,
                    'enrollment_type' => $profile->enrollment_type,
                    'has_failing'     => $profile->has_failing,
                    'has_discipline'  => $profile->has_discipline,
                    'income_bracket'  => $profile->income_bracket,
                ]);
                $mockApp->scholarship = $sch;
                $r = $aiCtrl->evaluate($mockApp);
                if ($r['score'] > $aiScore) { $aiScore = $r['score']; $eligibility = $r['eligibility']; }
                if ($r['eligibility'] === 'Eligible') $eligibleCount++;
            }
        } else {
            // No test data yet — fall back to the student's best evaluated application
            $best = $student ? ScholarshipApplication::where('student_id',$student->id)->orderByDesc('ai_score')->first() : null;
            if ($best && $best->ai_score > 0) {
                $aiScore = $best->ai_score;
                $eligibility = $best->ai_eligibility;
            }
        }

        return view('student.dashboard.index', compact(
            'myApplications','openScholarships','announcements','student',
            'profile','aiScore','eligibility','eligibleCount'
        ));
    }
}
