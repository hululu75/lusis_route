<?php

namespace App\Http\Controllers;

use App\Models\Rule;
use App\Helpers\ProjectHelper;
use App\Models\Delta;
use Illuminate\Http\Request;

class RuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Rule::with('delta')->withCount('routes')->latest();
        ProjectHelper::scopeToCurrentProject($query);
        $rules = $query->get();
        return view('rules.index', compact('rules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!ProjectHelper::hasCurrentProject()) {
            return redirect()->route('rules.index')
                ->with('error', 'Please select a project first');
        }

        $query = Delta::latest();
        ProjectHelper::scopeToCurrentProject($query);
        $deltas = $query->get();
        return view('rules.create', compact('deltas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:128|unique:rules',
            'class' => 'required|string|max:64',
            'type' => 'required|in:REQ,NOT,SAME,PUB,END',
            'delta_id' => 'nullable|exists:deltas,id',
            'on_failure' => 'nullable|string|max:128',
            'matching_cond' => 'nullable|string|max:128',
            'route_cond_ok' => 'nullable|string|max:128',
            'route_cond_ko' => 'nullable|string|max:128',
            'delta_next' => 'nullable|string|max:128',
            'delta_cond_ok' => 'nullable|string|max:128',
            'delta_cond_ko' => 'nullable|string|max:128',
            'description' => 'nullable|string',
        ]);

        $validated['project_id'] = ProjectHelper::getCurrentProjectId();

        if (!$validated['project_id']) {
            return redirect()->route('rules.index')
                ->with('error', 'Please select a project first');
        }

        Rule::create($validated);

        return redirect()->route('rules.index')
            ->with('success', 'Rule created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Rule $rule)
    {
        $rule->load(['delta', 'routes']);
        return view('rules.show', compact('rule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rule $rule)
    {
        $deltas = Delta::latest()->get();
        return view('rules.edit', compact('rule', 'deltas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rule $rule)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:128|unique:rules,name,' . $rule->id,
            'class' => 'required|string|max:64',
            'type' => 'required|in:REQ,NOT,SAME,PUB,END',
            'delta_id' => 'nullable|exists:deltas,id',
            'on_failure' => 'nullable|string|max:128',
            'matching_cond' => 'nullable|string|max:128',
            'route_cond_ok' => 'nullable|string|max:128',
            'route_cond_ko' => 'nullable|string|max:128',
            'delta_next' => 'nullable|string|max:128',
            'delta_cond_ok' => 'nullable|string|max:128',
            'delta_cond_ko' => 'nullable|string|max:128',
            'description' => 'nullable|string',
        ]);

        $rule->update($validated);

        return redirect()->route('rules.index')
            ->with('success', 'Rule updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rule $rule)
    {
        $rule->delete();

        return redirect()->route('rules.index')
            ->with('success', 'Rule deleted successfully!');
    }
}
