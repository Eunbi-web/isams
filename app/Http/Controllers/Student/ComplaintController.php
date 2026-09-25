<?php
namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
class ComplaintController extends Controller {
    public function index() {
        $complaints = Complaint::where('student_id', auth()->user()->student->id)->latest()->paginate(15);
        return view('student.complaints.index', compact('complaints'));
    }
    public function create() {
        return view('student.complaints.create');
    }
    public function store(Request $request) {
        $data = $request->validate([
            'type'         => 'required|in:Complaint,Report',
            'subject'      => 'required|string|max:255',
            'description'  => 'required|string|min:20',
            'is_anonymous' => 'nullable|boolean',
        ]);
        $student = auth()->user()->student;
        Complaint::create([
            'student_id'   => $student->id,
            'user_id'      => auth()->id(),
            'type'         => $data['type'],
            'subject'      => $data['subject'],
            'description'  => $data['description'],
            'is_anonymous' => (bool)($data['is_anonymous'] ?? false),
            'status'       => 'Pending',
        ]);
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'ok'       => true,
                'message'  => 'Your complaint has been submitted successfully.',
                'redirect' => route('student.complaints'),
            ]);
        }
        return redirect()->route('student.complaints')->with('success', 'Your complaint has been submitted successfully.');
    }
}
