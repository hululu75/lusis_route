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
        // Get the project ID from the route file
        $routeFile = RouteFile::findOrFail($request->routefile_id);
        $projectId = $routeFile->project_id;

        $validated = $request->validate([
            'routefile_id' => 'required|exists:route_files,id',
            'from_service_id' => [
                'required',
                'exists:services,id',
                function ($attribute, $value, $fail) use ($projectId) {
                    // Ensure service belongs to the same project
                    $service = Service::find($value);
                    if ($service && $service->project_id != $projectId) {
                        $fail('The selected service does not belong to the same project as the route file.');
                    }
                },
            ],
            'match_id' => [
                'nullable',
                'exists:matches,id',
                function ($attribute, $value, $fail) use ($request, $projectId) {
                    // Convert empty string to null for consistent checking
                    $matchId = ($value === '' || $value === null) ? null : $value;

                    if ($matchId) {
                        // Ensure match belongs to the same project
                        $match = RouteMatch::find($matchId);
                        if ($match && $match->project_id != $projectId) {
                            $fail('The selected match does not belong to the same project as the route file.');
                            return;
                        }

                        // Check if this service already has a route with the same match in this route file
                        $query = Route::where('routefile_id', $request->routefile_id)
                            ->where('from_service_id', $request->from_service_id)
                            ->where('match_id', $matchId);

                        if ($query->exists()) {
                            $fail('This service already has a route with the selected match in this route file.');
                        }
                    } else {
                        // Check for duplicate null match
                        if ($request->routefile_id && $request->from_service_id) {
                            $query = Route::where('routefile_id', $request->routefile_id)
                                ->where('from_service_id', $request->from_service_id)
                                ->whereNull('match_id');

                            if ($query->exists()) {
                                $fail('This service already has a route without match in this route file.');
                            }
                        }
                    }
                },
            ],
            'rule_id' => [
                'nullable',
                'exists:rules,id',
                function ($attribute, $value, $fail) use ($projectId) {
                    if ($value) {
                        // Ensure rule belongs to the same project
                        $rule = Rule::find($value);
                        if ($rule && $rule->project_id != $projectId) {
                            $fail('The selected rule does not belong to the same project as the route file.');
                        }
                    }
                },
            ],
            'chainclass' => 'nullable|string|max:128',
            'type' => 'nullable|in:REQ,NOT,SAME,PUB,END',
            'priority' => 'nullable|integer',
        ]);

        // Convert empty strings to null for nullable foreign keys
        if (isset($validated['match_id']) && $validated['match_id'] === '') {
            $validated['match_id'] = null;
        }
        if (isset($validated['rule_id']) && $validated['rule_id'] === '') {
            $validated['rule_id'] = null;
        }

        $route = Route::create($validated);

        // Check if request came from wizard (by checking referer)
        $referer = $request->headers->get('referer');
        if ($referer && str_contains($referer, '/wizard')) {
            return redirect()->route('route-files.wizard', $validated['routefile_id'])
                ->with('success', 'Route created successfully!');
        }

        return redirect()->route('routes.index')
            ->with('success', 'Route created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Route $route)
    {
        $route->load(['routeFile', 'service', 'match.conditions', 'rule']);
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
        // Get the project ID from the route file
        $routeFile = RouteFile::findOrFail($request->routefile_id);
        $projectId = $routeFile->project_id;

        $validated = $request->validate([
            'routefile_id' => 'required|exists:route_files,id',
            'from_service_id' => [
                'required',
                'exists:services,id',
                function ($attribute, $value, $fail) use ($projectId) {
                    // Ensure service belongs to the same project
                    $service = Service::find($value);
                    if ($service && $service->project_id != $projectId) {
                        $fail('The selected service does not belong to the same project as the route file.');
                    }
                },
            ],
            'match_id' => [
                'nullable',
                'exists:matches,id',
                function ($attribute, $value, $fail) use ($request, $route, $projectId) {
                    // Convert empty string to null for consistent checking
                    $matchId = ($value === '' || $value === null) ? null : $value;

                    if ($matchId) {
                        // Ensure match belongs to the same project
                        $match = RouteMatch::find($matchId);
                        if ($match && $match->project_id != $projectId) {
                            $fail('The selected match does not belong to the same project as the route file.');
                            return;
                        }

                        // Check if this service already has a route with the same match in this route file
                        $query = Route::where('routefile_id', $request->routefile_id)
                            ->where('from_service_id', $request->from_service_id)
                            ->where('id', '!=', $route->id)
                            ->where('match_id', $matchId);

                        if ($query->exists()) {
                            $fail('This service already has a route with the selected match in this route file.');
                        }
                    } else {
                        // Check for duplicate null match
                        if ($request->routefile_id && $request->from_service_id) {
                            $query = Route::where('routefile_id', $request->routefile_id)
                                ->where('from_service_id', $request->from_service_id)
                                ->where('id', '!=', $route->id)
                                ->whereNull('match_id');

                            if ($query->exists()) {
                                $fail('This service already has a route without match in this route file.');
                            }
                        }
                    }
                },
            ],
            'rule_id' => [
                'nullable',
                'exists:rules,id',
                function ($attribute, $value, $fail) use ($projectId) {
                    if ($value) {
                        // Ensure rule belongs to the same project
                        $rule = Rule::find($value);
                        if ($rule && $rule->project_id != $projectId) {
                            $fail('The selected rule does not belong to the same project as the route file.');
                        }
                    }
                },
            ],
            'chainclass' => 'nullable|string|max:128',
            'type' => 'nullable|in:REQ,NOT,SAME,PUB,END',
            'priority' => 'nullable|integer',
        ]);

        // Convert empty strings to null for nullable foreign keys
        if (isset($validated['match_id']) && $validated['match_id'] === '') {
            $validated['match_id'] = null;
        }
        if (isset($validated['rule_id']) && $validated['rule_id'] === '') {
            $validated['rule_id'] = null;
        }

        $route->update($validated);

        // Check if should return to wizard
        if ($request->input('return_to') === 'wizard' && $request->input('route_file_id')) {
            return redirect()->route('route-files.wizard', $request->input('route_file_id'))
                ->with('success', 'Route updated successfully!');
        }

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
