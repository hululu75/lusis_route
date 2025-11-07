<?php

namespace App\Http\Controllers;

use App\Models\Delta;
use Illuminate\Http\Request;

class DeltaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $deltas = Delta::withCount('rules')->latest()->get();
        return view('deltas.index', compact('deltas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('deltas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:128|unique:deltas',
            'next' => 'nullable|string|max:128',
            'definition' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

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
            'name' => 'required|string|max:128|unique:deltas,name,' . $delta->id,
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
