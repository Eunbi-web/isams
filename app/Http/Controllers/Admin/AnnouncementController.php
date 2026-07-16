<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::latest()->paginate(20);
        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body'  => 'required|string',
        ]);

        // Only insert columns that actually exist in the DB
        Announcement::create([
            'title'        => $request->title,
            'body'         => $request->body,
            'user_id'      => auth()->id(),
            'published_at' => now(),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Announcement published!', 'reload' => true]);
        }
        return redirect()->route('admin.announcements.index')->with('success', 'Announcement published!');
    }

    public function show(Announcement $announcement)
    {
        return view('admin.announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
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
        return redirect()->route('admin.announcements.index')->with('success', 'Announcement updated!');
    }

    public function destroy(Request $request, Announcement $announcement)
    {
        $announcement->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Announcement deleted.']);
        }
        return redirect()->route('admin.announcements.index')->with('success', 'Announcement deleted.');
    }
}
