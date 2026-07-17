<?php
namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;
use App\Models\{ScholarshipApplication,Scholarship};
use App\Http\Controllers\Admin\AiController;
use Illuminate\Http\Request;
class ApplicationController extends Controller {
    public function index() {
        $student = auth()->user()->student;
        $applications = $student ? ScholarshipApplication::where('student_id',$student->id)->with('scholarship')->latest()->get() : collect();
        return view('student.applications.index',compact('applications'));
    }
    public function create(int $scholarship) { $scholarship=Scholarship::findOrFail($scholarship); return view('student.applications.apply',compact('scholarship')); }
    public function store(Request $request) {
        $student = auth()->user()->student;
        if (!$student) return back()->with('error','Student profile not found.');
        $data = $request->validate(['scholarship_id'=>'required|exists:scholarships,id','gwa'=>'required|numeric|min:1|max:5','enrollment_type'=>'required|string','has_failing'=>'nullable','has_discipline'=>'nullable','income_bracket'=>'nullable|string','essay'=>'nullable|string','remarks'=>'nullable|string']);
        if (ScholarshipApplication::where('student_id',$student->id)->where('scholarship_id',$data['scholarship_id'])->exists()) return back()->with('error','You have already applied for this scholarship.');
        $app = new ScholarshipApplication($data);
        $app->student_id     = $student->id;
        $app->has_failing    = $request->boolean('has_failing');
        $app->has_discipline = $request->boolean('has_discipline');
        $app->income_bracket = $request->input('income_bracket','below_200');
        $app->scholarship_id = $data['scholarship_id'];

        $aiCtrl = new AiController();
        $result = $aiCtrl->evaluate($app);
        $app->ai_score=$result['score']; $app->ai_eligibility=$result['eligibility']; $app->ai_tag=$result['tag']; $app->ai_reasoning=$result['reasoning']; $app->ai_run_at=now(); $app->status='Pending';
        $app->save();
        $msg = "Application submitted! AI Score: {$result['score']}% — {$result['eligibility']}.";
        return redirect()->route('student.applications')->with('success',$msg);
    }
    public function show(int $id) {
        $student = auth()->user()->student;
        $application = ScholarshipApplication::where('student_id',$student?->id)->with('scholarship')->findOrFail($id);
        return view('student.applications.show',compact('application'));
    }
}
