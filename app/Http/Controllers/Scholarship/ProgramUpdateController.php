<?php
namespace App\Http\Controllers\Scholarship;
use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use App\Models\ScholarshipUpdate;
use Illuminate\Http\Request;

class ProgramUpdateController extends Controller {
    /** AJAX list of updates posted on a scholarship program. */
    public function index(Request $r, Scholarship $program) {
        $updates = ScholarshipUpdate::with('postedBy')
            ->where('scholarship_id', $program->id)
            ->latest()
            ->get()
            ->map(fn($u) => [
                'id'          => $u->id,
                'title'       => $u->title,
                'body'        => $u->body,
                'source_type' => $u->source_type,
                'posted_by'   => $u->postedBy?->name ?? 'Staff',
                'posted_at'   => $u->created_at->format('M d, Y g:i A'),
            ]);

        return response()->json(['updates' => $updates]);
    }

    /** Post a new update on a program (from a government / private / institutional report). */
    public function store(Request $r, Scholarship $program) {
        $data = $r->validate([
            'title'       => 'required|string|max:200',
            'body'        => 'required|string|max:5000',
            'source_type' => 'required|in:Government,Private,Institutional',
        ]);
        $update = ScholarshipUpdate::create($data + ['scholarship_id' => $program->id, 'user_id' => auth()->id()]);

        return response()->json([
            'message' => 'Update posted on '.$program->name.'.',
            'reload'  => true,
            'update'  => ['id' => $update->id],
        ]);
    }

    public function update(Request $r, ScholarshipUpdate $update) {
        $data = $r->validate([
            'title'       => 'required|string|max:200',
            'body'        => 'required|string|max:5000',
            'source_type' => 'required|in:Government,Private,Institutional',
        ]);
        $update->update($data);

        return response()->json(['message' => 'Update edited successfully.', 'reload' => true]);
    }

    public function destroy(Request $r, ScholarshipUpdate $update) {
        $update->delete();
        return response()->json(['message' => 'Update removed.', 'reload' => true]);
    }
}
