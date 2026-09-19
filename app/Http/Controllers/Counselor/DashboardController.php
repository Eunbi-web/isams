<?php
namespace App\Http\Controllers\Counselor;
use App\Http\Controllers\Controller;
use App\Models\CounselingSession;
use App\Models\Announcement;

class DashboardController extends Controller {
    public function index() {
        $inQueue           = CounselingSession::where('status','In Queue')->count();
        $scheduled         = CounselingSession::where('status','Scheduled')->count();
        $completed         = CounselingSession::where('status','Completed')->count();
        $totalAnnouncements= Announcement::count();
        $recent            = CounselingSession::with('student.user')->latest()->limit(8)->get();
        return view('counselor.dashboard.index', compact('inQueue','scheduled','completed','totalAnnouncements','recent'));
    }
}
