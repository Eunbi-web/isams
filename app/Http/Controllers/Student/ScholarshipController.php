<?php
namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use App\Models\ScrapedScholarship;
class ScholarshipController extends Controller {
    public function index() {
        $scholarships = Scholarship::with('applications')->where('status','Active')->latest()->get();
        $synced       = ScrapedScholarship::where('imported', false)
                          ->where('ai_confidence', '>=', 60)
                          ->where('is_open', true)
                          ->latest('last_scraped_at')
                          ->get();
        return view('student.scholarships.index', compact('scholarships','synced'));
    }
    public function show(int $id) {
        $scholarship = Scholarship::findOrFail($id);
        return view('student.scholarships.show', compact('scholarship'));
    }
}