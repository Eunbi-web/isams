<?php
namespace App\Http\Controllers\Scholarship;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller {
    public function index() {
        // Two round trips total for all dashboard counts (cloud DB adds ~0.5s per query)
        $appAgg = (array) DB::table('scholarship_applications')->selectRaw("
            SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) AS pending,
            SUM(CASE WHEN ai_eligibility = 'Eligible' THEN 1 ELSE 0 END) AS eligible,
            SUM(CASE WHEN ai_eligibility = 'For Review' THEN 1 ELSE 0 END) AS for_review,
            SUM(CASE WHEN ai_run_at::date = ? THEN 1 ELSE 0 END) AS processed_today
        ", [today()->toDateString()])->first();
        $agg = DB::selectOne("
            SELECT
              (SELECT COUNT(*) FROM scholarships WHERE status = 'Active') AS programs,
              (SELECT COUNT(*) FROM scraped_scholarships WHERE imported = false) AS synced
        ");
        $stats = [
            'programs'       => (int) $agg->programs,
            'pending'        => (int) ($appAgg['pending'] ?? 0),
            'eligible'       => (int) ($appAgg['eligible'] ?? 0),
            'for_review'     => (int) ($appAgg['for_review'] ?? 0),
            'processed_today'=> (int) ($appAgg['processed_today'] ?? 0),
            'synced'         => (int) $agg->synced,
        ];
        return view('scholarship.dashboard.index', compact('stats'));
    }
}
