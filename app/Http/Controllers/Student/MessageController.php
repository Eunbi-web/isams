<?php
namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;
use App\Models\AdminMessage;
use Illuminate\Http\Request;
class MessageController extends Controller {
    public function index() {
        $student = auth()->user()->student;
        $query = AdminMessage::where('student_id', $student->id)->latest();
        $messages = $query->paginate(15);
        // Visiting the inbox marks everything as read (one-way messaging: read-only for students)
        AdminMessage::where('student_id', $student->id)->where('is_read', false)->update(['is_read' => true, 'read_at' => now()]);
        return view('student.messages.index', compact('messages'));
    }
    public function show(Request $request, AdminMessage $message) {
        abort_unless($message->student_id === auth()->user()->student?->id, 404);
        if (!$message->is_read) {
            $message->update(['is_read' => true, 'read_at' => now()]);
        }
        return view('student.messages.show', compact('message'));
    }
}
