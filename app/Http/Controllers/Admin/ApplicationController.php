<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{ScholarshipApplication,Student,Scholarship};
use Illuminate\Http\Request;
class ApplicationController extends Controller {
    public function index(Request $r) {

        $applications = ScholarshipApplication::with(['student','scholarship'])
            ->when($r->search,fn($q,$s)=>$q->whereHas('student',fn($sq)=>$sq->where('first_name','like',"%$s%")->orWhere('last_name','like',"%$s%")))
            // CHANGE 5: status filter accepts the new On Review / Canceled values too;
            // 'Approved' also matches legacy 'Scholarship Granted' rows
            ->when($r->status,fn($q,$s)=>$q->when($s==='Approved',
                fn($qq)=>$qq->whereIn('status',['Approved','Scholarship Granted']),
                fn($qq)=>$qq->where('status',$s)))
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
        // scholarship_id is already set on the model via make($data) — assigning a
        // model object to the $app->scholarship relation name made Eloquent try
        // to insert a non-existent "scholarship" column (SQLSTATE 42703)
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

    // CHANGE 5: updateStatus now recognizes the full new status set
    // (Pending, On Review, Approved, Rejected, Canceled). Statuses are stored
    // exactly as chosen — 'Approved' stays 'Approved' so the new status filter
    // and badges match; the student portal already displays Approved as granted.
    public function updateStatus(Request $r, ScholarshipApplication $application) {
        $data = $r->validate([
            // 'For Review' kept for legacy links/buttons that still post it
            'status' => 'required|in:Pending,On Review,Approved,Rejected,Canceled,For Review',
        ]);
        $newStatus = $data['status'];
        $application->update(['status' => $newStatus]);
        return back()->with('success', "Status updated to {$newStatus}!");
    }

    // ADDED: approve/reject handlers — the routes existed but the methods were
    // missing, so the Approve/Reject buttons in the AI Filter and applications
    // index threw a server error when clicked.
    public function approve(Request $r, ScholarshipApplication $application) {
        $application->update(['status' => 'Approved']);
        if ($r->ajax() || $r->wantsJson()) {
            return response()->json(['message' => 'Status updated', 'status' => 'Approved']);
        }
        return back()->with('success', 'Application approved!');
    }
    public function reject(Request $r, ScholarshipApplication $application) {
        $application->update(['status' => 'Rejected']);
        if ($r->ajax() || $r->wantsJson()) {
            return response()->json(['message' => 'Status updated', 'status' => 'Rejected']);
        }
        return back()->with('success', 'Application rejected.');
    }

    // CHANGE 5: bulk approve now stores the canonical 'Approved' status
    public function bulkApprove() {
        $count = ScholarshipApplication::where('ai_eligibility','Eligible')
            ->whereIn('status',['Pending','On Review'])
            ->update(['status'=>'Approved']);
        return back()->with('success',"{$count} eligible applications approved!");
    }

    // ADDED: bulk reject handler — the route existed but the method was missing
    public function bulkReject() {
        $count = ScholarshipApplication::where('ai_eligibility','Not Eligible')
            ->where('status','Pending')
            ->update(['status'=>'Rejected']);
        return back()->with('success',"{$count} applications rejected.");
    }

}
