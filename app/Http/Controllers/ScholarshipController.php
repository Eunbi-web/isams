<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scholarship;
use App\Models\Student;

class ScholarshipController extends Controller
{
    public function index(Request $request)
    {
        $scholarships = Scholarship::withCount('grantees')
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%$s%"))
            ->when($request->type,   fn($q, $t) => $q->where('type', $t))
            ->latest()
            ->paginate(20);

        return view('scholarships.index', compact('scholarships'));
    }

    public function create()
    {
        return view('scholarships.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:200',
            'type'          => 'required|string|max:50',
            'description'   => 'nullable|string',
            'benefits'      => 'nullable|string',
            'requirements'  => 'nullable|string',
            'slots'         => 'nullable|integer|min:0',
            'amount'        => 'nullable|numeric|min:0',
            'source'        => 'nullable|string|max:200',
            'start_date'    => 'nullable|date',
            'end_date'      => 'nullable|date',
            'status'        => 'required|string|max:30',
        ]);

        Scholarship::create($data);

        return redirect()->route('scholarships.index')
            ->with('success', 'Scholarship program added!');
    }

    public function show(Scholarship $scholarship)
    {
        $scholarship->load('grantees.student');
        return view('scholarships.show', compact('scholarship'));
    }

    public function edit(Scholarship $scholarship)
    {
        return view('scholarships.edit', compact('scholarship'));
    }

    public function update(Request $request, Scholarship $scholarship)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:200',
            'type'        => 'required|string|max:50',
            'description' => 'nullable|string',
            'benefits'    => 'nullable|string',
            'slots'       => 'nullable|integer|min:0',
            'amount'      => 'nullable|numeric|min:0',
            'status'      => 'required|string|max:30',
        ]);

        $scholarship->update($data);

        return redirect()->route('scholarships.show', $scholarship)
            ->with('success', 'Scholarship updated!');
    }

    public function destroy(Scholarship $scholarship)
    {
        $scholarship->delete();
        return redirect()->route('scholarships.index')
            ->with('success', 'Scholarship removed.');
    }

    public function grantees(Scholarship $scholarship)
    {
        $scholarship->load('grantees.student');
        $available = Student::whereDoesntHave('scholarships', fn($q) => $q->where('scholarship_id', $scholarship->id))->get();
        return view('scholarships.grantees', compact('scholarship', 'available'));
    }
}
