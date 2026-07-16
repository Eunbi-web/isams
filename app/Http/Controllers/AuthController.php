<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LoginLog;
class AuthController extends Controller {
    public function showLogin()   { return view('auth.login'); }
    public function forgotPassword() { return view('auth.forgot-password'); }
    public function sendResetLink(Request $r) {
        $r->validate(['email'=>'required|email']);
        return back()->with('status','If this email exists, a reset link has been sent.');
    }
    public function login(Request $request) {
        $request->validate(['email'=>'required|email','password'=>'required']);
        $user = \App\Models\User::where('email',$request->email)->first();
        if ($user && !$user->is_active) {
            LoginLog::create(['user_id'=>$user->id,'email'=>$request->email,'ip_address'=>$request->ip(),'user_agent'=>$request->userAgent(),'status'=>'blocked','role'=>$user->role,'logged_in_at'=>now()]);
            return back()->withErrors(['email'=>'Your account has been deactivated. Contact the administrator.'])->onlyInput('email');
        }
        if (Auth::attempt(['email'=>$request->email,'password'=>$request->password],$request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();
            $user->update(['last_login_at'=>now(),'last_login_ip'=>$request->ip()]);
            LoginLog::create(['user_id'=>$user->id,'email'=>$user->email,'ip_address'=>$request->ip(),'user_agent'=>$request->userAgent(),'status'=>'success','role'=>$user->role,'logged_in_at'=>now()]);
            return match($user->role) {
                'superadmin'       => redirect()->route('superadmin.dashboard'),
                'admin','officer'  => redirect()->route('admin.dashboard'),
                'student'          => redirect()->route('student.dashboard'),
                default            => redirect('/'),
            };
        }
        LoginLog::create(['email'=>$request->email,'ip_address'=>$request->ip(),'user_agent'=>$request->userAgent(),'status'=>'failed','logged_in_at'=>now()]);
        return back()->withErrors(['email'=>'Invalid email or password.'])->onlyInput('email');
    }
    public function logout(Request $request) {
        if (Auth::check()) {
            LoginLog::where('user_id',Auth::id())->whereNull('logged_out_at')->latest()->first()?->update(['logged_out_at'=>now()]);
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
