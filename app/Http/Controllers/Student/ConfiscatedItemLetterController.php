<?php
namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;
use App\Models\ConfiscatedItemLetter;
use Illuminate\Http\Request;
class ConfiscatedItemLetterController extends Controller {
    public function index() {
        $letters = ConfiscatedItemLetter::where('student_id', auth()->user()->student->id)->latest()->paginate(10);
        return view('student.letters.index', compact('letters'));
    }
    public function create() {
        $student = auth()->user()->student()->with('user')->first();
        return view('student.letters.create', compact('student'));
    }
    public function store(Request $request) {
        $data = $request->validate([
            'item_description'   => 'required|string|max:500',
            'date_confiscated'   => 'nullable|date',
            'confiscated_by'     => 'nullable|string|max:255',
            'reason_confiscated' => 'nullable|string',
            'letter_content'     => 'required|string|min:50',
        ]);
        ConfiscatedItemLetter::create([
            'student_id'         => auth()->user()->student->id,
            'user_id'            => auth()->id(),
            'item_description'   => $data['item_description'],
            'date_confiscated'   => $data['date_confiscated'] ?? null,
            'confiscated_by'     => $data['confiscated_by'] ?? null,
            'reason_confiscated' => $data['reason_confiscated'] ?? null,
            'letter_content'     => $data['letter_content'],
            'status'             => 'Submitted',
        ]);
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'ok'       => true,
                'message'  => 'Letter submitted successfully.',
                'redirect' => route('student.letters'),
            ]);
        }
        return redirect()->route('student.letters')->with('success', 'Letter submitted successfully.');
    }
    public function show(ConfiscatedItemLetter $letter) {
        abort_unless($letter->student_id === auth()->user()->student?->id, 404);
        return view('student.letters.show', compact('letter'));
    }
}
