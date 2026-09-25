<?php
namespace App\Http\Controllers\Scholarship;
use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use App\Models\ScrapedScholarship;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $query = Scholarship::withCount('applications');
        if ($request->filled('search')) $query->where('name','like','%'.$request->search.'%');
        if ($request->filled('type'))   $query->where('type', $request->type);
        if ($request->filled('status')) $query->where('status', $request->status);
        $scholarships = $query->latest()->paginate(20);
        return view('scholarship.programs.index', compact('scholarships'));
    }

    public function create()
    {
        return view('scholarship.programs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:300',
            'type'    => 'required|string',
            'status'  => 'required|string',
            'benefits'=> 'required|string',
        ]);

        $criteria = [
            'gwa_max'       => (float)$request->input('ai_gwa_max', 1.75),
            'income_max'    => (int)$request->input('ai_income_max', 400000),
            'no_failing'    => $request->boolean('ai_no_failing'),
            'no_discipline' => $request->boolean('ai_no_discipline'),
        ];

        Scholarship::create([
            'name'         => $request->name,
            'type'         => $request->type,
            'status'       => $request->status,
            'benefits'     => $request->benefits,
            'description'  => $request->description,
            'requirements' => $request->requirements,
            'slots'        => $request->slots,
            'amount'       => $request->amount,
            'source'       => $request->source,
            'end_date'     => $request->end_date ?: null,
            'ai_criteria'  => json_encode($criteria),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message'=>'Scholarship created!','redirect'=>route('scholarship.programs.index')]);
        }
        return redirect()->route('scholarship.programs.index')->with('success','Scholarship created!');
    }

    public function show(Request $request, Scholarship $scholarship)
    {
        $scholarship->load('applications.student');

        // Quick-view modal on the Programs list: return only the details markup
        if ($request->boolean('modal')) {
            return view('scholarship.programs._details', compact('scholarship'))->render();
        }

        return view('scholarship.programs.show', compact('scholarship'));
    }

    public function edit(Scholarship $scholarship)
    {
        return view('scholarship.programs.edit', compact('scholarship'));
    }

    public function update(Request $request, Scholarship $scholarship)
    {
        // FIX: use only() with explicit safe fields so _method=PUT never causes delete
        $request->validate([
            'name'    => 'required|string|max:300',
            'type'    => 'required|string',
            'status'  => 'required|string',
            'benefits'=> 'required|string',
        ]);

        $criteria = [
            'gwa_max'       => (float)$request->input('ai_gwa_max', 1.75),
            'income_max'    => (int)$request->input('ai_income_max', 400000),
            'no_failing'    => $request->boolean('ai_no_failing'),
            'no_discipline' => $request->boolean('ai_no_discipline'),
        ];

        // FIX: explicit field list — never passes _method or other spoofed fields
        $scholarship->update([
            'name'         => $request->name,
            'type'         => $request->type,
            'status'       => $request->status,
            'benefits'     => $request->benefits,
            'description'  => $request->description,
            'requirements' => $request->requirements,
            'slots'        => $request->slots,
            'amount'       => $request->amount,
            'source'       => $request->source,
            'end_date'     => $request->end_date ?: null,
            'ai_criteria'  => json_encode($criteria),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message'=>'Scholarship updated!','redirect'=>route('scholarship.programs.index')]);
        }
        return redirect()->route('scholarship.programs.index')->with('success','Scholarship updated!');
    }

    public function destroy(Request $request, Scholarship $scholarship)
    {
        // FIX: Reset imported flag on any scraped scholarship linked to this one
        ScrapedScholarship::where('scholarship_id', $scholarship->id)
            ->update([
                'imported'        => false,
                'scholarship_id'  => null,
            ]);

        $scholarship->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message'=>'Scholarship deleted. Synced scholarship marked as unimported.']);
        }
        return redirect()->route('scholarship.programs.index')
            ->with('success','Scholarship deleted. You can re-import from PH Scholarship Sync.');
    }
}
