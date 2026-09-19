<?php
namespace App\Http\Controllers\Counselor;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller {
    public function index() {
        $notifications = Notification::where('user_id', auth()->id())->latest()->paginate(20);
        return view('counselor.notifications.index', compact('notifications'));
    }

    public function dropdown() {
        $notifications = Notification::where('user_id', auth()->id())->latest()->take(15)->get();
        $unread = Notification::where('user_id', auth()->id())->where('read', false)->count();
        return response()->json(compact('notifications','unread'));
    }

    public function markRead(Request $r, $id) {
        $notif = Notification::where('id', $id)->where('user_id', auth()->id())->first();
        if ($notif) $notif->update(['read' => true]);
        return response()->json(['message' => 'Marked as read.']);
    }

    public function markAllRead(Request $r) {
        Notification::where('user_id', auth()->id())->where('read', false)->update(['read' => true]);
        return response()->json(['message' => 'All marked as read.']);
    }
}
