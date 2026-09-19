<?php
namespace App\Http\Controllers\Counselor;
use App\Http\Controllers\Controller;
use App\Models\Announcement;

class AnnouncementController extends Controller {
    public function index() {
        $announcements = Announcement::orderBy('published_at','desc')->get();
        return view('counselor.announcements.index', compact('announcements'));
    }
}
