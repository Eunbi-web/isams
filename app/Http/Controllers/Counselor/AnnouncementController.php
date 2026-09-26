<?php
namespace App\Http\Controllers\Counselor;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller {
    public function index() {
        $announcements = Announcement::orderBy('published_at','desc')->get();
        return view('counselor.announcements.index', compact('announcements'));
    }

    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'body'  => 'required|string',
        ]);

        Announcement::create([
            'title'        => $request->title,
            'body'         => $request->body,
            'user_id'      => auth()->id(),
            'published_at' => now(),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Announcement published!', 'reload' => true]);
        }
        return redirect()->route('counselor.announcements')->with('success', 'Announcement published!');
    }

    public function update(Request $request, Announcement $announcement) {
        $request->validate([
            'title' => 'required|string|max:255',
            'body'  => 'required|string',
        ]);

        $announcement->update([
            'title' => $request->title,
            'body'  => $request->body,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Announcement updated!', 'reload' => true]);
        }
        return redirect()->route('counselor.announcements')->with('success', 'Announcement updated!');
    }

    public function destroy(Request $request, Announcement $announcement) {
        $announcement->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Announcement deleted.', 'reload' => true]);
        }
        return redirect()->route('counselor.announcements')->with('success', 'Announcement deleted.');
    }
}
