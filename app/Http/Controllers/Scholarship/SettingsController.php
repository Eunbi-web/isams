<?php
namespace App\Http\Controllers\Scholarship;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class SettingsController extends Controller {
    public function index()  { return view('scholarship.settings.index'); }

    public function update(Request $r) {
        $user = $r->user();
        $data = $r->validate([
            'name'             => 'required|string|max:200',
            // NOTE: users live in the users_all table, not "users"
            'email'            => 'required|email|unique:users_all,email,'.$user->id,
            'contact_number'   => 'nullable|string|max:30',
            'department'       => 'nullable|string|max:100',
            'current_password' => 'nullable|string',
            'password'         => 'nullable|string|min:8|confirmed',
        ]);

        if ($r->filled('password')) {
            if (!$r->filled('current_password') || !Hash::check($r->current_password, $user->password)) {
                return back()->withInput()->with('error', 'The current password you entered is incorrect.');
            }
            $data['password'] = Hash::make($r->password);
        } else {
            unset($data['password'], $data['current_password']);
        }

        $user->update(collect($data)->only(['name','email','contact_number','department','password'])->all());

        return back()->with('success', 'Settings saved successfully.');
    }
}
