<?php
namespace App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use Illuminate\Http\Request;
class LogController extends Controller {
    public function index(Request $r) {
        $logs=LoginLog::with('user')->when($r->search,fn($q,$s)=>$q->where('email','like',"%$s%")->orWhere('ip_address','like',"%$s%"))->when($r->status,fn($q,$s)=>$q->where('status',$s))->when($r->role,fn($q,$role)=>$q->where('role',$role))->latest('logged_in_at')->paginate(30);
        return view('superadmin.logs.index',compact('logs'));
    }
}
