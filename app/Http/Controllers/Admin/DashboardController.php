<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{ScholarshipApplication,Scholarship,CounselingSession,Complaint,DisciplineCase};
use Illuminate\Support\Facades\DB;
use App\Models\User;
class DashboardController extends Controller {
    public function index() {
        // Single round trip for scholarship_applications counts
        $appAgg = (array) DB::table('scholarship_applications')->selectRaw("
            SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) AS pending,
            SUM(CASE WHEN status IN ('Approved','Scholarship Granted') THEN 1 ELSE 0 END) AS approved,
            COUNT(DISTINCT student_id) AS applied_students
        ")->first();
        $stats = [
            'programs'        => Scholarship::where('status','Active')->count(),
            'pending'         => (int)($appAgg['pending'] ?? 0),
            'active_scholars' => (int)($appAgg['approved'] ?? 0),
            'counseling_queue'=> (int) CounselingSession::where('status','In Queue')->count(),
        ];
        // Single round trip for the new DSA counts
        $dsaAgg = DB::selectOne("
            SELECT
              (SELECT COUNT(*) FROM users_all WHERE role = 'student') AS student_accounts,
              (SELECT COUNT(*) FROM counseling_sessions WHERE status IN ('Pending','In Queue')) AS pending_counseling,
              (SELECT COUNT(*) FROM discipline_cases) AS discipline_cases,
              (SELECT COUNT(*) FROM complaints) AS complaints
        ");
        $totalStudentAccounts = (int) $dsaAgg->student_accounts;
        $studentsApplied      = (int) ($appAgg['applied_students'] ?? 0);
        $pendingCounseling    = (int) $dsaAgg->pending_counseling;
        $totalDisciplineCases = (int) $dsaAgg->discipline_cases;
        $totalComplaints      = (int) $dsaAgg->complaints;
        // Eager-loaded rows for the monitoring tables (shared with the Recent Applications table)
        $recentApps = ScholarshipApplication::with(['student','scholarship'])->latest()->take(8)->get();
        $pendingSessions = CounselingSession::with('student')->whereIn('status',['Pending','In Queue'])->latest()->take(5)->get();
        return view('admin.dashboard.index', compact(
            'stats','totalStudentAccounts','studentsApplied','pendingCounseling','totalDisciplineCases','totalComplaints','recentApps','pendingSessions'
        ));
    }
}
