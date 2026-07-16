<?php
namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;
use App\Models\CounselingSession;
use Illuminate\Http\Request;
class CounselingController extends Controller {
    public function index() {
        $student  = auth()->user()->student;
        $sessions = $student ? CounselingSession::where('student_id',$student->id)->latest()->get() : collect();
        $queuePos = CounselingSession::where('status','In Queue')->count();
        return view('student.counseling.index',compact('sessions','queuePos'));
    }
    public function store(Request $request) {
        $request->validate(['concern_type'=>'required|string','priority'=>'required|string','concern_detail'=>'nullable|string','preferred_date'=>'nullable|date|after:today','preferred_time'=>'nullable|string']);
        $student = auth()->user()->student;
        if (!$student) return back()->with('error','Student profile not found.');
        $queuePos = CounselingSession::where('status','In Queue')->count() + 1;
        CounselingSession::create(['student_id'=>$student->id,'concern_type'=>$request->concern_type,'concern_detail'=>$request->concern_detail,'priority'=>$request->priority,'preferred_date'=>$request->preferred_date,'preferred_time'=>$request->preferred_time,'queue_position'=>$queuePos,'status'=>'In Queue']);
        return back()->with('success',"Request submitted! You are #{$queuePos} in the queue. A counselor will be assigned shortly.");
    }
}
