<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        return view('notifications.index');
    }

    public function markRead(Request $request, int $id)
    {
        auth()->user()->notifications()->where('id', $id)->update(['read_at' => now()]);
        return back()->with('success', 'Notification marked as read.');
    }
}
