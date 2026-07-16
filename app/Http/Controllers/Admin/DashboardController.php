<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{ScholarshipApplication,Scholarship,Student,CounselingSession};
class DashboardController extends Controller {
    public function index() {
        $stats = [
            'programs'       => Scholarship::where('status','Active')->count(),
            'eligible'       => ScholarshipApplication::where('ai_eligibility','Eligible')->count(),
            'for_review'     => ScholarshipApplication::where('ai_eligibility','For Review')->count(),
            'not_eligible'   => ScholarshipApplication::where('ai_eligibility','Not Eligible')->count(),
            'pending'        => ScholarshipApplication::where('status','Pending')->count(),
            'active_scholars'=> ScholarshipApplication::where('status','Approved')->count(),
            'renewal'        => ScholarshipApplication::where('ai_tag','Renewal Ready')->count(),
            'processed_today'=> ScholarshipApplication::whereDate('ai_run_at',today())->count(),
            'counseling_queue'=> CounselingSession::where('status','In Queue')->count(),
        ];
        return view('admin.dashboard.index', compact('stats'));
    }
}
