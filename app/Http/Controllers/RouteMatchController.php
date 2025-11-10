<?php

namespace App\Http\Controllers;

use App\Models\RouteMatch;
use App\Helpers\ProjectHelper;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RouteMatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = RouteMatch::withCount(['conditions', 'routes'])->latest();
        ProjectHelper::scopeToCurrentProject($query);
        $matches = $query->get();

        // Return embed view for iframe display
        if ($request->get('embed')) {
            return view('matches.embed', compact('matches'));
        }

        return view('matches.index', compact('matches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!ProjectHelper::hasCurrentProject()) {
            return redirect()->route('matches.index')
                ->with('error', 'Please select a project first');
        }
        return view('matches.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $projectId = ProjectHelper::getCurrentProjectId();

        if (!$projectId) {
            return redirect()->route('matches.index')
                ->with('error', 'Please select a project first');
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:128',
                Rule::unique('matches')->where(function ($query) use ($projectId) {
                    return $query->where('project_id', $projectId);
                }),
            ],
            'type' => 'nullable|in:REQ,NOT,SAME,PUB,END',
            'description' => 'nullable|string',
        ]);

        $validated['project_id'] = $projectId;

        RouteMatch::create($validated);

        return redirect()->route('matches.index')
            ->with('success', 'Match created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(RouteMatch $match)
    {
        $match->load(['conditions', 'routes']);
        return view('matches.show', compact('match'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RouteMatch $match)
    {
        return view('matches.edit', compact('match'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RouteMatch $match)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:128',
                Rule::unique('matches')->where(function ($query) use ($match) {
                    return $query->where('project_id', $match->project_id);
                })->ignore($match->id),
            ],
            'type' => 'nullable|in:REQ,NOT,SAME,PUB,END',
            'description' => 'nullable|string',
        ]);

        $match->update($validated);

        return redirect()->route('matches.index')
            ->with('success', 'Match updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RouteMatch $match)
    {
        $match->delete();

        return redirect()->route('matches.index')
            ->with('success', 'Match deleted successfully!');
    }
}
