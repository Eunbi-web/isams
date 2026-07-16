<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()->paginate(20);
        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Return unread notifications as JSON for the dropdown.
     */
    public function dropdown()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()->take(15)->get();
        $unread = Notification::where('user_id', auth()->id())
            ->where('read', false)->count();
        return response()->json([
            'notifications' => $notifications,
            'unread'        => $unread,
        ]);
    }

    public function markRead(Request $request, $id)
    {
        $notif = Notification::where('id', $id)
            ->where('user_id', auth()->id())->first();
        if ($notif) $notif->update(['read' => true]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Marked as read.']);
        }
        return back();
    }

    public function markAllRead(Request $request)
    {
        Notification::where('user_id', auth()->id())
            ->where('read', false)
            ->update(['read' => true]);

        return response()->json(['message' => 'All marked as read.']);
    }
}
