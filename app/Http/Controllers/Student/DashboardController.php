<?php
namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;
use App\Models\{ScholarshipApplication,Scholarship,Announcement};
class DashboardController extends Controller {
    public function index() {
        $student = auth()->user()->student;
        $myApplications = $student ? ScholarshipApplication::where('student_id',$student->id)->with('scholarship')->latest()->take(3)->get() : collect();
        $openScholarships = Scholarship::where('status','Active')->take(3)->get();
        $announcements = Announcement::latest()->take(3)->get();
        return view('student.dashboard.index', compact('myApplications','openScholarships','announcements','student'));
    }
}
