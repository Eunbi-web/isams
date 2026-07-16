<?php
namespace App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Controller;
use App\Models\{User,LoginLog,ScholarshipApplication,Student,CounselingSession,Scholarship};
class MonitoringController extends Controller {
    public function index() {
        $stats=['users_online_today'=>LoginLog::whereDate('logged_in_at',today())->where('status','success')->distinct('user_id')->count('user_id'),'total_applications'=>ScholarshipApplication::count(),'eligible_count'=>ScholarshipApplication::where('ai_eligibility','Eligible')->count(),'scholarships_active'=>Scholarship::where('status','Active')->count(),'counseling_queue'=>CounselingSession::where('status','In Queue')->count(),'total_students'=>Student::count(),'login_today'=>LoginLog::whereDate('logged_in_at',today())->count(),'failed_logins_today'=>LoginLog::whereDate('logged_in_at',today())->where('status','failed')->count()];
        $recent_activity=LoginLog::with('user')->latest('logged_in_at')->take(15)->get();
        $logins_by_hour=LoginLog::whereDate('logged_in_at',today())->selectRaw("EXTRACT(HOUR FROM logged_in_at) as hour, COUNT(*) as count")->groupBy('hour')->orderBy('hour')->pluck('count','hour');
        return view('superadmin.monitoring.index',compact('stats','recent_activity','logins_by_hour'));
    }
}
