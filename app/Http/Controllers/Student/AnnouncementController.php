<?php
namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
class AnnouncementController extends Controller {
    public function index() { $announcements=Announcement::latest()->paginate(15); return view('student.announcements.index',compact('announcements')); }
    public function show(int $id) { $announcement=Announcement::findOrFail($id); return view('student.announcements.show',compact('announcement')); }
}
