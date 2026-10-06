<?php
namespace App\Http\Controllers\Counselor;
use App\Http\Controllers\Controller;
use App\Models\CounselingSession;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller {
    public function index() {
        // Single round trip for all dashboard counts (cloud DB adds ~0.5s per query)
        $agg = DB::selectOne("
            SELECT
              (SELECT COUNT(*) FROM counseling_sessions WHERE status = 'In Queue')  AS in_queue,
              (SELECT COUNT(*) FROM counseling_sessions WHERE status = 'Scheduled') AS scheduled,
              (SELECT COUNT(*) FROM counseling_sessions WHERE status = 'Completed') AS completed,
              (SELECT COUNT(*) FROM announcements_all) AS total_announcements
        ");
        $inQueue            = (int) $agg->in_queue;
        $scheduled          = (int) $agg->scheduled;
        $completed          = (int) $agg->completed;
        $totalAnnouncements = (int) $agg->total_announcements;
        $recent = CounselingSession::with('student.user')->latest()->limit(8)->get();
        return view('counselor.dashboard.index', compact('inQueue','scheduled','completed','totalAnnouncements','recent'));
    }
}
