<?php
namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class ProfileController extends Controller {
    public function index()  { return view('student.profile.index'); }
    public function update(Request $r) {
        $user=$r->user();
        $r->validate(['password'=>'nullable|min:6|confirmed']);
        if ($r->filled('password')) {
            $user->update(['password'=>Hash::make($r->password)]);
        }
        return back()->with('success','Profile updated!');
    }
}
