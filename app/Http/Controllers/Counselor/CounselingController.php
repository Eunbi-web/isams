<?php
namespace App\Http\Controllers\Counselor;
use App\Http\Controllers\Controller;
use App\Models\CounselingSession;
use Illuminate\Http\Request;

class CounselingController extends Controller {
    public function index(Request $r) {
        $sessions = CounselingSession::with('student')
            ->when($r->search, function($q,$s){
                $q->where(function($qq) use ($s){
                    $qq->where('concern_type','like',"%$s%")
                       ->orWhereHas('student', fn($st)=>$st->where('first_name','like',"%$s%")
                           ->orWhere('last_name','like',"%$s%"));
                });
            })
            ->when($r->status,   fn($q,$v)=>$q->where('status',$v))
            ->when($r->priority, fn($q,$v)=>$q->where('priority',$v))
            ->orderByRaw("CASE priority WHEN 'Emergency' THEN 0 WHEN 'Urgent' THEN 1 ELSE 2 END")
            ->orderBy('preferred_date')
            ->paginate(20);

        $stats = [
            'inQueue'   => CounselingSession::where('status','In Queue')->count(),
            'scheduled' => CounselingSession::where('status','Scheduled')->count(),
            'completed' => CounselingSession::where('status','Completed')->count(),
            'total'     => CounselingSession::count(),
        ];

        return view('counselor.counseling.index', compact('sessions','stats'));
    }

    public function schedule(Request $r, CounselingSession $counseling) {
        $data = $r->validate([
            'session_date' => 'required|date',
            'session_time' => 'nullable|string|max:50',
            'venue'        => 'nullable|string|max:200',
            'priority'     => 'nullable|in:Normal,Urgent,Emergency',
            'notes'        => 'nullable|string',
        ]);
        $counseling->update($data + ['status'=>'Scheduled','counselor_id'=>auth()->id()]);

        if ($r->wantsJson() || $r->ajax()) {
            return response()->json(['message'=>'Session scheduled successfully.','reload'=>true]);
        }
        return redirect()->route('counselor.counseling.index')->with('success','Session scheduled successfully.');
    }

    public function complete(Request $r, CounselingSession $counseling) {
        $data = $r->validate([
            'notes'              => 'required|string',
            'follow_up_required' => 'nullable|boolean',
            'follow_up_date'     => 'nullable|date|required_if:follow_up_required,1',
        ]);
        $counseling->update([
            'notes'              => $data['notes'],
            'status'             => 'Completed',
            'follow_up_required' => $r->boolean('follow_up_required'),
            'follow_up_date'     => $r->boolean('follow_up_required') ? ($data['follow_up_date'] ?? null) : null,
        ]);

        if ($r->wantsJson() || $r->ajax()) {
            return response()->json(['message'=>'Session marked as completed.','reload'=>true]);
        }
        return redirect()->route('counselor.counseling.index')->with('success','Session marked as completed.');
    }

    public function destroy(Request $r, CounselingSession $counseling) {
        $counseling->delete();
        if ($r->wantsJson() || $r->ajax()) {
            return response()->json(['message'=>'Counseling request removed.']);
        }
        return redirect()->route('counselor.counseling.index')->with('success','Counseling request removed.');
    }
}
