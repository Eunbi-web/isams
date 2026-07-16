<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class SettingsController extends Controller {
    public function index()  { return view('admin.settings.index'); }
    public function update(Request $r) {
        $user=$r->user();
        $r->validate(['name'=>'required','email'=>'required|email|unique:users,email,'.$user->id]);
        $data=$r->only('name','email');
        if ($r->filled('current_password')&&Hash::check($r->current_password,$user->password)&&$r->filled('password')) { $data['password']=Hash::make($r->password); }
        $user->update($data);
        return back()->with('success','Settings saved!');
    }
}
