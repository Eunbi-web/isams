<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\ConfiscatedItemLetter;
use Illuminate\Http\Request;
class ConfiscatedItemLetterController extends Controller {
    public function index(Request $request) {
        $query = ConfiscatedItemLetter::with('student.user')->latest();
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) {
            $s = $request->string('search')->trim()->toString();
            $query->where(function ($q) use ($s) {
                $q->where('item_description', 'like', "%{$s}%")
                  ->orWhereHas('student', function ($sq) use ($s) {
                      $sq->where('first_name', 'like', "%{$s}%")
                         ->orWhere('last_name', 'like', "%{$s}%")
                         ->orWhere('student_id', 'like', "%{$s}%");
                  });
            });
        }
        $letters = $query->paginate(20)->withQueryString();
        $stats = [
            'total'        => ConfiscatedItemLetter::count(),
            'submitted'    => ConfiscatedItemLetter::where('status','Submitted')->count(),
            'under_review' => ConfiscatedItemLetter::where('status','Under Review')->count(),
            'approved'     => ConfiscatedItemLetter::where('status','Approved')->count(),
        ];
        $filters = ['status' => $request->status, 'search' => $request->search];
        return view('admin.letters.index', compact('letters','stats','filters'));
    }
    public function show(ConfiscatedItemLetter $letter) {
        $letter->load('student.user');
        $reviewer = $letter->reviewed_by ? \App\Models\User::find($letter->reviewed_by) : null;
        return view('admin.letters.show', compact('letter','reviewer'));
    }
    public function updateStatus(Request $request, ConfiscatedItemLetter $letter) {
        $data = $request->validate([
            'status'     => 'required|in:Under Review,Approved,Rejected',
            'admin_notes'=> 'nullable|string|max:2000',
        ]);
        $letter->update([
            'status'      => $data['status'],
            'admin_notes' => $data['admin_notes'] ?? null,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'message' => "Letter marked as {$data['status']}."]);
        }
        return back()->with('success', "Letter marked as {$data['status']}.");
    }
}
