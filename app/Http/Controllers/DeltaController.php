<?php

namespace App\Http\Controllers;

use App\Models\Delta;
use App\Helpers\ProjectHelper;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeltaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Delta::withCount('rules')->latest();
        ProjectHelper::scopeToCurrentProject($query);
        $deltas = $query->get();
        return view('deltas.index', compact('deltas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!ProjectHelper::hasCurrentProject()) {
            return redirect()->route('deltas.index')
                ->with('error', 'Please select a project first');
        }
        return view('deltas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $projectId = ProjectHelper::getCurrentProjectId();

        if (!$projectId) {
            return redirect()->route('deltas.index')
                ->with('error', 'Please select a project first');
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:128',
                Rule::unique('deltas')->where(function ($query) use ($projectId) {
                    return $query->where('project_id', $projectId);
                }),
            ],
            'next' => 'nullable|string|max:128',
            'definition' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $validated['project_id'] = $projectId;

        Delta::create($validated);

        return redirect()->route('deltas.index')
            ->with('success', 'Delta created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Delta $delta)
    {
        $delta->load('rules');
        return view('deltas.show', compact('delta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Delta $delta)
    {
        return view('deltas.edit', compact('delta'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Delta $delta)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:128',
                Rule::unique('deltas')->where(function ($query) use ($delta) {
                    return $query->where('project_id', $delta->project_id);
                })->ignore($delta->id),
            ],
            'next' => 'nullable|string|max:128',
            'definition' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $delta->update($validated);

        return redirect()->route('deltas.index')
            ->with('success', 'Delta updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Delta $delta)
    {
        $delta->delete();

        return redirect()->route('deltas.index')
            ->with('success', 'Delta deleted successfully!');
    }
}
