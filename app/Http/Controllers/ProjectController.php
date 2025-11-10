<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Service;
use App\Models\Delta;
use App\Models\RouteMatch;
use App\Models\MatchCondition;
use App\Models\Rule;
use App\Models\RouteFile;
use App\Models\Route;
use App\Helpers\ProjectHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::withCount('routeFiles')->latest()->get();
        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::latest()->get();
        return view('projects.create', compact('projects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:128|unique:projects',
            'description' => 'nullable|string',
            'copy_from_project' => 'nullable|boolean',
            'copy_project_id' => 'nullable|required_if:copy_from_project,1|exists:projects,id',
        ]);

        try {
            DB::beginTransaction();

            // Create the new project
            $newProject = Project::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
            ]);

            // Copy data if requested
            if (isset($validated['copy_from_project']) && $validated['copy_from_project'] && isset($validated['copy_project_id'])) {
                $sourceProject = Project::findOrFail($validated['copy_project_id']);
                $this->copyProjectData($sourceProject, $newProject);
            }

            DB::commit();

            $message = isset($validated['copy_from_project']) && $validated['copy_from_project']
                ? 'Project created successfully with copied data!'
                : 'Project created successfully!';

            return redirect()->route('projects.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create project: ' . $e->getMessage());
        }
    }

    /**
     * Copy all data from source project to new project
     */
    private function copyProjectData(Project $sourceProject, Project $newProject)
    {
        // Maps to track old ID => new ID relationships
        $serviceMap = [];
        $deltaMap = [];
        $matchMap = [];
        $ruleMap = [];
        $routeFileMap = [];

        // 1. Copy Services
        $services = Service::where('project_id', $sourceProject->id)->get();
        foreach ($services as $service) {
            $newService = Service::create([
                'project_id' => $newProject->id,
                'name' => $service->name,
                'description' => $service->description,
            ]);
            $serviceMap[$service->id] = $newService->id;
        }

        // 2. Copy Deltas
        $deltas = Delta::where('project_id', $sourceProject->id)->get();
        foreach ($deltas as $delta) {
            $newDelta = Delta::create([
                'project_id' => $newProject->id,
                'name' => $delta->name,
                'next' => $delta->next,
                'definition' => $delta->definition,
                'description' => $delta->description,
            ]);
            $deltaMap[$delta->id] = $newDelta->id;
        }

        // 3. Copy Matches and their Conditions
        $matches = RouteMatch::where('project_id', $sourceProject->id)->get();
        foreach ($matches as $match) {
            $newMatch = RouteMatch::create([
                'project_id' => $newProject->id,
                'name' => $match->name,
                'type' => $match->type,
                'description' => $match->description,
            ]);
            $matchMap[$match->id] = $newMatch->id;

            // Copy conditions for this match
            $conditions = MatchCondition::where('match_id', $match->id)->get();
            foreach ($conditions as $condition) {
                MatchCondition::create([
                    'match_id' => $newMatch->id,
                    'field' => $condition->field,
                    'operator' => $condition->operator,
                    'value' => $condition->value,
                ]);
            }
        }

        // 4. Copy Rules (with delta references)
        $rules = Rule::where('project_id', $sourceProject->id)->get();
        foreach ($rules as $rule) {
            $newRule = Rule::create([
                'project_id' => $newProject->id,
                'name' => $rule->name,
                'class' => $rule->class,
                'type' => $rule->type,
                'delta_id' => $rule->delta_id ? ($deltaMap[$rule->delta_id] ?? null) : null,
                'on_failure' => $rule->on_failure,
                'matching_cond' => $rule->matching_cond,
                'route_cond_ok' => $rule->route_cond_ok,
                'route_cond_ko' => $rule->route_cond_ko,
                'delta_next' => $rule->delta_next,
                'delta_cond_ok' => $rule->delta_cond_ok,
                'delta_cond_ko' => $rule->delta_cond_ko,
                'description' => $rule->description,
            ]);
            $ruleMap[$rule->id] = $newRule->id;
        }

        // 5. Copy Route Files and their Routes
        $routeFiles = RouteFile::where('project_id', $sourceProject->id)->get();
        foreach ($routeFiles as $routeFile) {
            $newRouteFile = RouteFile::create([
                'project_id' => $newProject->id,
                'name' => $routeFile->name,
                'file_name' => $routeFile->file_name,
                'description' => $routeFile->description,
            ]);
            $routeFileMap[$routeFile->id] = $newRouteFile->id;

            // Copy routes for this route file
            $routes = Route::where('routefile_id', $routeFile->id)->get();
            foreach ($routes as $route) {
                Route::create([
                    'routefile_id' => $newRouteFile->id,
                    'from_service_id' => $route->from_service_id ? ($serviceMap[$route->from_service_id] ?? null) : null,
                    'match_id' => $route->match_id ? ($matchMap[$route->match_id] ?? null) : null,
                    'rule_id' => $route->rule_id ? ($ruleMap[$route->rule_id] ?? null) : null,
                    'chainclass' => $route->chainclass,
                    'type' => $route->type,
                    'priority' => $route->priority,
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $project->load(['routeFiles.routes']);
        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:128|unique:projects,name,' . $project->id,
            'description' => 'nullable|string',
        ]);

        $project->update($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Project updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        // If deleting the currently selected project, clear the session
        if (ProjectHelper::getCurrentProjectId() === $project->id) {
            Session::forget('current_project_id');
        }

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully!');
    }
}
