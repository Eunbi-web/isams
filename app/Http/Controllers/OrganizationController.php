<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organization;

class OrganizationController extends Controller
{
    public function index(Request $request)
    {
        $organizations = Organization::withCount('members')
            ->when($request->search, fn($q, $s) =>
                $q->where('name', 'like', "%$s%")
                  ->orWhere('description', 'like', "%$s%"))
            ->when($request->type, fn($q, $t) => $q->where('type', $t))
            ->latest()
            ->paginate(20);

        return view('organizations.index', compact('organizations'));
    }

    public function create()
    {
        return view('organizations.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:200|unique:organizations',
            'acronym'     => 'nullable|string|max:20',
            'type'        => 'required|string|max:50',
            'description' => 'nullable|string',
            'adviser'     => 'nullable|string|max:200',
            'president'   => 'nullable|string|max:200',
            'year_founded'=> 'nullable|integer|min:1900|max:' . date('Y'),
            'status'      => 'required|string|max:30',
        ]);

        Organization::create($data);

        return redirect()->route('organizations.index')
            ->with('success', 'Organization registered!');
    }

    public function show(Organization $organization)
    {
        $organization->load('members');
        return view('organizations.show', compact('organization'));
    }

    public function edit(Organization $organization)
    {
        return view('organizations.edit', compact('organization'));
    }

    public function update(Request $request, Organization $organization)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:200',
            'acronym'     => 'nullable|string|max:20',
            'type'        => 'required|string|max:50',
            'description' => 'nullable|string',
            'adviser'     => 'nullable|string|max:200',
            'president'   => 'nullable|string|max:200',
            'status'      => 'required|string|max:30',
        ]);

        $organization->update($data);

        return redirect()->route('organizations.show', $organization)
            ->with('success', 'Organization updated!');
    }

    public function destroy(Organization $organization)
    {
        $organization->delete();
        return redirect()->route('organizations.index')
            ->with('success', 'Organization removed.');
    }
}
