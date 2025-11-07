<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\RouteFile;
use App\Models\Service;
use App\Models\RouteMatch;
use App\Models\Rule;
use App\Helpers\ProjectHelper;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Route::with(['routeFile', 'service', 'match', 'rule']);

        // Filter by project through routeFile
        if ($projectId = ProjectHelper::getCurrentProjectId()) {
            $query->whereHas('routeFile', function($q) use ($projectId) {
                $q->where('project_id', $projectId);
            });
        }

        $routes = $query->orderBy('priority')->get();
        return view('routes.index', compact('routes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get route files for current project
        $routeFilesQuery = RouteFile::latest();
        ProjectHelper::scopeToCurrentProject($routeFilesQuery);
        $routeFiles = $routeFilesQuery->get();

        // Get services for current project
        $servicesQuery = Service::latest();
        ProjectHelper::scopeToCurrentProject($servicesQuery);
        $services = $servicesQuery->get();

        // Get matches for current project
        $matchesQuery = RouteMatch::latest();
        ProjectHelper::scopeToCurrentProject($matchesQuery);
        $matches = $matchesQuery->get();

        // Get rules for current project
        $rulesQuery = Rule::latest();
        ProjectHelper::scopeToCurrentProject($rulesQuery);
        $rules = $rulesQuery->get();

        return view('routes.create', compact('routeFiles', 'services', 'matches', 'rules'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'routefile_id' => 'required|exists:route_files,id',
            'from_service_id' => 'required|exists:services,id',
            'match_id' => 'nullable|exists:matches,id',
            'rule_id' => 'nullable|exists:rules,id',
            'chainclass' => 'nullable|string|max:128',
            'type' => 'nullable|in:REQ,NOT,SAME,PUB,END',
            'priority' => 'nullable|integer',
        ]);

        Route::create($validated);

        return redirect()->route('routes.index')
            ->with('success', 'Route created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Route $route)
    {
        $route->load(['routeFile', 'service', 'match', 'rule']);
        return view('routes.show', compact('route'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Route $route)
    {
        $routeFiles = RouteFile::latest()->get();
        $services = Service::latest()->get();
        $matches = RouteMatch::latest()->get();
        $rules = Rule::latest()->get();
        return view('routes.edit', compact('route', 'routeFiles', 'services', 'matches', 'rules'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Route $route)
    {
        $validated = $request->validate([
            'routefile_id' => 'required|exists:route_files,id',
            'from_service_id' => 'required|exists:services,id',
            'match_id' => 'nullable|exists:matches,id',
            'rule_id' => 'nullable|exists:rules,id',
            'chainclass' => 'nullable|string|max:128',
            'type' => 'nullable|in:REQ,NOT,SAME,PUB,END',
            'priority' => 'nullable|integer',
        ]);

        $route->update($validated);

        return redirect()->route('routes.index')
            ->with('success', 'Route updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Route $route)
    {
        $route->delete();

        return redirect()->route('routes.index')
            ->with('success', 'Route deleted successfully!');
    }

    /**
     * Reorder routes by priority (for drag-and-drop).
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:routes,id',
        ]);

        foreach ($validated['ids'] as $priority => $id) {
            Route::where('id', $id)->update(['priority' => $priority]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Routes reordered successfully!',
        ]);
    }
}
