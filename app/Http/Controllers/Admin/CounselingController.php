<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\CounselingSession;
use Illuminate\Http\Request;
class CounselingController extends Controller {
    public function index(Request $r) {
        $sessions=CounselingSession::with(['student','counselor'])->when($r->status,fn($q,$s)=>$q->where('status',$s))->orderByRaw("CASE priority WHEN 'Urgent' THEN 0 WHEN 'Medium' THEN 1 ELSE 2 END")->orderBy('queue_position')->paginate(20);
        return view('admin.counseling.index',compact('sessions'));
    }
    public function schedule(Request $r, CounselingSession $counseling) {
        $r->validate(['session_date'=>'required|date','session_time'=>'required|string']);
        $counseling->update(['session_date'=>$r->session_date,'session_time'=>$r->session_time,'venue'=>$r->venue??'Guidance Office','counselor_id'=>$r->counselor_id,'notes'=>$r->notes,'status'=>'Scheduled']);
        return back()->with('success','Session scheduled! Student notified.');
    }
    public function complete(CounselingSession $counseling) { $counseling->update(['status'=>'Completed']); return back()->with('success','Session marked as completed.'); }
}
