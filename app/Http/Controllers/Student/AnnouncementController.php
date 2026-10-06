<?php
namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
class AnnouncementController extends Controller {
    // announcements auto-archive 30 days after posting
    private function active() { return Announcement::where('created_at','>=',now()->subDays(30)); }
    public function index() { $announcements=$this->active()->latest()->paginate(15); return view('student.announcements.index',compact('announcements')); }
    public function show(int $id) { $announcement=$this->active()->findOrFail($id); return view('student.announcements.show',compact('announcement')); }
}
