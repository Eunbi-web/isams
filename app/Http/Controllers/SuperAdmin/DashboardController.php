<?php
namespace App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller {
    public function index() {
        // Three round trips total for all stats (cloud DB adds ~0.5s per query):
        // one grouped query for every user stat + role breakdown, one for login/app counts
        $roleAgg = DB::table('users_all')
            ->selectRaw("role, COUNT(*) AS n, SUM(CASE WHEN is_active THEN 1 ELSE 0 END) AS active_n")
            ->groupBy('role')->get();
        $usersByRole  = $roleAgg->pluck('n','role');
        $logAppCounts = DB::selectOne("
            SELECT
              (SELECT COUNT(DISTINCT user_id) FROM login_logs WHERE status = 'success' AND logged_in_at::date = ?) AS online_today,
              (SELECT COUNT(*) FROM login_logs WHERE status = 'success') AS total_logins,
              (SELECT COUNT(*) FROM login_logs WHERE status = 'failed') AS failed_logins,
              (SELECT COUNT(*) FROM scholarship_applications) AS applications
        ", [today()->toDateString()]);
        $stats = [
            'total_users'  => (int) $roleAgg->sum('n'),
            'active_users' => (int) $roleAgg->sum('active_n'),
            'students'     => (int) ($usersByRole['student'] ?? 0),
            'staff'        => (int) ($usersByRole['admin'] ?? 0) + ($usersByRole['officer'] ?? 0),
            'online_today' => (int) $logAppCounts->online_today,
            'total_logins' => (int) $logAppCounts->total_logins,
            'failed_logins'=> (int) $logAppCounts->failed_logins,
            'applications' => (int) $logAppCounts->applications,
        ];
        $recentLogins = LoginLog::with('user')->latest('logged_in_at')->take(10)->get();
        return view('superadmin.dashboard.index', compact('stats','recentLogins','usersByRole'));
    }
}
