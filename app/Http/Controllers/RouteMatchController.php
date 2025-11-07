<?php

namespace App\Http\Controllers;

use App\Models\RouteMatch;
use Illuminate\Http\Request;

class RouteMatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $matches = RouteMatch::withCount(['conditions', 'routes'])->latest()->get();
        return view('matches.index', compact('matches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('matches.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:128|unique:matches',
            'type' => 'nullable|in:REQ,NOT,SAME,PUB,END',
            'description' => 'nullable|string',
        ]);

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
            'name' => 'required|string|max:128|unique:matches,name,' . $match->id,
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
