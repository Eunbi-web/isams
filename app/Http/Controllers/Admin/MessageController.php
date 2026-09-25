<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AdminMessage;
use App\Models\Student;
use App\Models\Notification;
use Illuminate\Http\Request;
class MessageController extends Controller {
    public function index() {
        $messages = AdminMessage::with('student.user')->latest()->paginate(20);
        $total  = AdminMessage::count();
        $unread = AdminMessage::where('is_read', false)->count();
        return view('admin.messages.index', compact('messages','total','unread'));
    }
    public function create() {
        $students = Student::with('user')->orderBy('last_name')->get();
        return view('admin.messages.create', compact('students'));
    }
    public function store(Request $request) {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject'    => 'required|string|max:255',
            'body'       => 'required|string|min:5',
        ]);
        $message = AdminMessage::create([
            'sent_by'    => auth()->id(),
            'student_id' => $data['student_id'],
            'subject'    => $data['subject'],
            'body'       => $data['body'],
            'is_read'    => false,
        ]);
        $student = Student::find($data['student_id']);
        if ($student && $student->user_id) {
            Notification::create([
                'user_id' => $student->user_id,
                'type'    => 'system',
                'title'   => 'New Message from Admin',
                'message' => $data['subject'],
                'read'    => false,
            ]);
        }
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'message' => 'Message sent successfully.', 'redirect' => route('admin.messages.index')]);
        }
        return redirect()->route('admin.messages.index')->with('success', 'Message sent successfully.');
    }
}
