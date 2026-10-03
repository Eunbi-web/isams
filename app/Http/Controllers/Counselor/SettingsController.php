<?php
namespace App\Http\Controllers\Counselor;
use App\Http\Controllers\Controller;
use App\Models\CounselingSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller {
    public function index() {
        return view('counselor.settings.index', ['setting' => CounselingSetting::current()]);
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

    /** Toggle between queue-based scheduling and time slot booking. */
    public function updateScheduling(Request $r) {
        $data = $r->validate([
            'scheduling_mode' => 'required|in:queue,slots',
            'slot_capacity'   => 'nullable|integer|min:1|max:10',
        ]);
        $setting = CounselingSetting::current();
        $setting->update([
            'scheduling_mode' => $data['scheduling_mode'],
            'slot_capacity'   => $data['slot_capacity'] ?? $setting->slot_capacity,
            'counselor_id'    => auth()->id(),
        ]);
        return response()->json(['message' => $data['scheduling_mode'] === 'slots'
            ? 'Time slot booking is now active. Students book a specific date and time slot.'
            : 'Queue-based scheduling is now active. Students are auto-queued on request.']);
    }
}
