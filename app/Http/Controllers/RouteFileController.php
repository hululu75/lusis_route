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
        $currentProjectId = \App\Helpers\ProjectHelper::getCurrentProjectId();
        return view('route_files.create', compact('projects', 'currentProjectId'));
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

        $routeFile = RouteFile::create($validated);

        // Redirect to setup wizard instead of index
        return redirect()->route('route-files.wizard', $routeFile->id)
            ->with('success', 'Route file created successfully! Let\'s set up your routes.');
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

        return redirect()->route('route-files.index')
            ->with('success', 'Route file updated successfully!');
    }

    /**
     * Show the setup wizard for a new route file
     */
    public function wizard(RouteFile $routeFile)
    {
        $routeFile->load(['project', 'routes.service', 'routes.match', 'routes.rule']);

        // Get counts of existing entities for this project
        $stats = [
            'services' => \App\Models\Service::where('project_id', $routeFile->project_id)->count(),
            'matches' => \App\Models\RouteMatch::where('project_id', $routeFile->project_id)->count(),
            'deltas' => \App\Models\Delta::where('project_id', $routeFile->project_id)->count(),
            'rules' => \App\Models\Rule::where('project_id', $routeFile->project_id)->count(),
            'routes' => $routeFile->routes->count(),
        ];

        // Get available options for route creation
        $services = \App\Models\Service::where('project_id', $routeFile->project_id)->latest()->get();
        $matches = \App\Models\RouteMatch::where('project_id', $routeFile->project_id)->latest()->get();
        $rules = \App\Models\Rule::where('project_id', $routeFile->project_id)->latest()->get();

        return view('route_files.wizard', compact('routeFile', 'stats', 'services', 'matches', 'rules'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RouteFile $routeFile)
    {
        try {
            $name = $routeFile->name;
            $routeFile->delete();

            return redirect()->route('route-files.index')
                ->with('success', "Route file '{$name}' and its routes were deleted successfully!");
        } catch (\Exception $e) {
            return redirect()->route('route-files.index')
                ->with('error', 'Failed to delete route file: ' . $e->getMessage());
        }
    }
}
