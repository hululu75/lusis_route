<?php

namespace App\Http\Controllers;

use App\Models\RouteFile;
use App\Models\Project;
use Illuminate\Http\Request;

class RouteFileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $routeFiles = RouteFile::with('project')->withCount('routes')->latest()->get();
        return view('route_files.index', compact('routeFiles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::latest()->get();
        return view('route_files.create', compact('projects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:128',
            'file_name' => 'required|string|max:128',
            'description' => 'nullable|string',
        ]);

        RouteFile::create($validated);

        return redirect()->route('route_files.index')
            ->with('success', 'Route file created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(RouteFile $routeFile)
    {
        $routeFile->load(['project', 'routes']);
        return view('route_files.show', compact('routeFile'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RouteFile $routeFile)
    {
        $projects = Project::latest()->get();
        return view('route_files.edit', compact('routeFile', 'projects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RouteFile $routeFile)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:128',
            'file_name' => 'required|string|max:128',
            'description' => 'nullable|string',
        ]);

        $routeFile->update($validated);

        return redirect()->route('route_files.index')
            ->with('success', 'Route file updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RouteFile $routeFile)
    {
        $routeFile->delete();

        return redirect()->route('route_files.index')
            ->with('success', 'Route file deleted successfully!');
    }
}
