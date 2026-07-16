<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{ScholarshipApplication,Student,Scholarship};
use Illuminate\Http\Request;
class ApplicationController extends Controller {
    public function index(Request $r) {
        $applications = ScholarshipApplication::with(['student','scholarship'])
            ->when($r->search,fn($q,$s)=>$q->whereHas('student',fn($sq)=>$sq->where('first_name','like',"%$s%")->orWhere('last_name','like',"%$s%")))
            ->when($r->status,fn($q,$s)=>$q->where('status',$s))
            ->when($r->ai_eligibility,fn($q,$e)=>$q->where('ai_eligibility',$e))
            ->latest()->paginate(25);
        return view('admin.applications.index',compact('applications'));
    }
    public function create() { $students=Student::orderBy('last_name')->get(); $scholarships=Scholarship::where('status','Active')->get(); return view('admin.applications.create',compact('students','scholarships')); }
    public function store(Request $request) {
        $data=$request->validate(['student_id'=>'required|exists:students,id','scholarship_id'=>'required|exists:scholarships,id','gwa'=>'required|numeric|min:1|max:5','enrollment_type'=>'required|string','has_failing'=>'nullable|boolean','has_discipline'=>'nullable|boolean','income_bracket'=>'nullable|string','remarks'=>'nullable|string','status'=>'required|string']);
        $app=ScholarshipApplication::make($data);
        $app->has_failing=$request->boolean('has_failing');
        $app->has_discipline=$request->boolean('has_discipline');
        $app->income_bracket=$request->input('income_bracket','below_200');
        $app->scholarship=Scholarship::find($data['scholarship_id']);
        $aiCtrl=new AiController();
        $result=$aiCtrl->evaluate($app);
        $app->ai_score=$result['score']; $app->ai_eligibility=$result['eligibility']; $app->ai_tag=$result['tag']; $app->ai_reasoning=$result['reasoning']; $app->ai_run_at=now();
        $app->save();
        return redirect()->route('admin.applications.index')->with('success',"Submitted! AI Score: {$result['score']}% — {$result['eligibility']}");
    }
    public function show(ScholarshipApplication $application) { $application->load(['student','scholarship']); return view('admin.applications.show',compact('application')); }
    public function edit(ScholarshipApplication $application) { $students=Student::orderBy('last_name')->get(); $scholarships=Scholarship::all(); return view('admin.applications.edit',compact('application','students','scholarships')); }
    public function update(Request $r, ScholarshipApplication $application) { $application->update($r->only(['status','remarks'])); return redirect()->route('admin.applications.show',$application)->with('success','Updated!'); }
    public function destroy(ScholarshipApplication $application) { $application->delete(); return redirect()->route('admin.applications.index')->with('success','Deleted.'); }
    public function updateStatus(Request $r, ScholarshipApplication $application) { $application->update(['status'=>$r->status]); return back()->with('success',"Status updated to {$r->status}!"); }
    public function bulkApprove() { $count=ScholarshipApplication::where('ai_eligibility','Eligible')->where('status','Pending')->update(['status'=>'Approved']); return back()->with('success',"{$count} eligible applications approved!"); }
}
