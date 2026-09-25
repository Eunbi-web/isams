<?php
namespace App\Http\Controllers\Scholarship;
use App\Http\Controllers\Controller;
use App\Models\{ScholarshipApplication,Scholarship,ScrapedScholarship};
class DashboardController extends Controller {
    public function index() {
        $stats = [
            'programs'       => Scholarship::where('status','Active')->count(),
            'pending'        => ScholarshipApplication::where('status','Pending')->count(),
            'eligible'       => ScholarshipApplication::where('ai_eligibility','Eligible')->count(),
            'for_review'     => ScholarshipApplication::where('ai_eligibility','For Review')->count(),
            'processed_today'=> ScholarshipApplication::whereDate('ai_run_at',today())->count(),
            'synced'         => ScrapedScholarship::where('imported',false)->count(),
        ];
        return view('scholarship.dashboard.index', compact('stats'));
    }
}
