<?php
namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class ProfileController extends Controller {
    public function index()  { return view('student.profile.index'); }
    public function update(Request $r) {
        $user=$r->user();
        $r->validate(['name'=>'required','email'=>'required|email|unique:users,email,'.$user->id]);
        $data=$r->only('name','email');
        if ($r->filled('password')) $data['password']=Hash::make($r->password);
        $user->update($data);
        return back()->with('success','Profile updated!');
    }
}
