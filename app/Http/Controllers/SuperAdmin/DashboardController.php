<?php
namespace App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Controller;
use App\Models\{User,LoginLog,Student,ScholarshipApplication};
class DashboardController extends Controller {
    public function index() {
        $stats=['total_users'=>User::count(),'active_users'=>User::where('is_active',true)->count(),'students'=>User::where('role','student')->count(),'staff'=>User::whereIn('role',['admin','officer'])->count(),'online_today'=>LoginLog::whereDate('logged_in_at',today())->where('status','success')->distinct('user_id')->count('user_id'),'total_logins'=>LoginLog::where('status','success')->count(),'failed_logins'=>LoginLog::where('status','failed')->count(),'applications'=>ScholarshipApplication::count()];
        $recentLogins=LoginLog::with('user')->latest('logged_in_at')->take(10)->get();
        $usersByRole=User::selectRaw('role, COUNT(*) as count')->groupBy('role')->pluck('count','role');
        return view('superadmin.dashboard.index',compact('stats','recentLogins','usersByRole'));
    }
}
