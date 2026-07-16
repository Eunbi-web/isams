<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name'                  => 'required|string|max:200',
            'email'                 => 'required|email|unique:users,email,' . $user->id,
            'current_password'      => 'nullable|string',
            'password'              => 'nullable|string|min:8|confirmed',
            'theme'                 => 'nullable|in:system,light,dark',
        ]);

        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }
        }

        unset($data['current_password'], $data['password_confirmation']);

        // Persist UI theme preference (system/light/dark)
        if ($request->filled('theme')) {
            $user->theme = $request->input('theme');
        }

        $user->update($data);

        return back()->with('success', 'Settings updated!');
    }
}
