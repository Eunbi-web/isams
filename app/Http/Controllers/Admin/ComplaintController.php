<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
class ComplaintController extends Controller {
    public function index(Request $request) {
        $query = Complaint::with('student.user')->latest();
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('type'))   $query->where('type', $request->type);
        if ($request->filled('search')) {
            $s = $request->string('search')->trim()->toString();
            $query->where(function ($q) use ($s) {
                $q->where('subject', 'like', "%{$s}%")
                  ->orWhereHas('student', function ($sq) use ($s) {
                      $sq->where('first_name', 'like', "%{$s}%")
                         ->orWhere('last_name', 'like', "%{$s}%")
                         ->orWhere('student_id', 'like', "%{$s}%");
                  });
            });
        }
        $complaints = $query->paginate(20)->withQueryString();
        $stats = [
            'total'        => Complaint::count(),
            'pending'      => Complaint::where('status','Pending')->count(),
            'under_review' => Complaint::where('status','Under Review')->count(),
            'resolved'     => Complaint::where('status','Resolved')->count(),
        ];
        $filters = ['status' => $request->status, 'type' => $request->type, 'search' => $request->search];
        return view('admin.complaints.index', compact('complaints','stats','filters'));
    }
    public function show(Complaint $complaint) {
        $complaint->load('student.user');
        return view('admin.complaints.show', compact('complaint'));
    }
    public function reply(Request $request, Complaint $complaint) {
        $data = $request->validate(['reply' => 'required|string|min:5']);
        $complaint->update([
            'admin_reply' => $data['reply'],
            'replied_at'  => now(),
            'replied_by'  => auth()->id(),
            'status'      => 'Resolved',
        ]);
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'message' => 'Reply sent and complaint marked as resolved.']);
        }
        return back()->with('success', 'Reply sent and complaint marked as resolved.');
    }
    public function updateStatus(Request $request, Complaint $complaint) {
        $data = $request->validate(['status' => 'required|in:Pending,Under Review,Resolved,Dismissed']);
        $complaint->update(['status' => $data['status']]);
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'message' => "Status updated to {$data['status']}."]);
        }
        return back()->with('success', "Status updated to {$data['status']}.");
    }
}
