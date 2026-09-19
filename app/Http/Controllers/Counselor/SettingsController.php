<?php
namespace App\Http\Controllers\Counselor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller {
    public function index() {
        return view('counselor.settings.index');
    }

    public function update(Request $r) {
        $user = auth()->user();
        $data = $r->validate([
            'name'             => 'required|string|max:200',
            'email'            => 'required|email|unique:users_all,email,'.$user->id,
            'current_password' => 'nullable|string',
            'password'         => 'nullable|string|min:8|confirmed',
        ]);

        if ($r->filled('password') && $r->filled('current_password') && Hash::check($r->current_password, $user->password)) {
            $data['password'] = bcrypt($r->password);
        } else {
            unset($data['password']);
        }

        $user->update(collect($data)->only('name','email','password')->all());
        return back()->with('success','Settings saved successfully.');
    }
}
