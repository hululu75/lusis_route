<?php

namespace App\Http\Controllers;

use App\Models\RouteFile;
use App\Models\Project;
use App\Helpers\ProjectHelper;
use Illuminate\Http\Request;

class RouteFileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = RouteFile::with('project')->withCount('routes')->latest();
        ProjectHelper::scopeToCurrentProject($query);
        $routeFiles = $query->get();
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
        $routeFile->load(['project', 'routes.service', 'routes.match.conditions', 'routes.rule.delta']);

        // Generate XML preview
        $xmlPreview = $this->generateXmlPreview($routeFile);

        return view('route_files.show', compact('routeFile', 'xmlPreview'));
    }

    /**
     * Generate aligned XML preview for route file
     */
    private function generateXmlPreview(RouteFile $routeFile)
    {
        $xml = [];
        $xml[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml[] = '<routing>';

        // Group routes by service
        $routesByService = $routeFile->routes->groupBy('from_service_id');

        // Collect unique matches, rules, deltas
        $matches = collect();
        $rules = collect();
        $deltas = collect();

        foreach ($routeFile->routes as $route) {
            if ($route->match) $matches->push($route->match);
            if ($route->rule) $rules->push($route->rule);
            if ($route->rule && $route->rule->delta) $deltas->push($route->rule->delta);
        }

        $matches = $matches->unique('id');
        $rules = $rules->unique('id');
        $deltas = $deltas->unique('id');

        // Export Routes with aligned cases
        if ($routesByService->count() > 0) {
            $xml[] = '  <routes>';

            foreach ($routesByService as $serviceId => $routes) {
                $service = $routes->first()->service;
                if (!$service) continue;

                $xml[] = '    <route class="' . htmlspecialchars($service->name) . '">';

                // Calculate max widths for case attributes
                $maxCond = 0;
                $maxRule = 0;
                foreach ($routes as $route) {
                    if ($route->match) $maxCond = max($maxCond, strlen($route->match->name));
                    if ($route->rule) $maxRule = max($maxRule, strlen($route->rule->name));
                }

                // Output aligned cases
                foreach ($routes->sortBy('priority') as $route) {
                    $attrs = [];
                    if ($route->match) {
                        $condValue = htmlspecialchars($route->match->name);
                        $attrs[] = 'cond="' . str_pad($condValue . '"', $maxCond + 1);
                    }
                    if ($route->rule) {
                        $ruleValue = htmlspecialchars($route->rule->name);
                        $attrs[] = 'rule="' . str_pad($ruleValue . '"', $maxRule + 1);
                    }
                    if ($route->chainclass) {
                        $attrs[] = 'chainclass="' . htmlspecialchars($route->chainclass) . '"';
                    }

                    if (count($attrs) > 0) {
                        $xml[] = '      <case ' . implode(' ', $attrs) . '/>';
                    } else {
                        $xml[] = '      <case/>';
                    }
                }

                $xml[] = '    </route>';
            }

            $xml[] = '  </routes>';
        }

        // Export Matches with aligned attributes
        if ($matches->count() > 0) {
            $xml[] = '  <matches>';

            // Calculate max widths
            $maxName = 0;
            foreach ($matches as $match) {
                $maxName = max($maxName, strlen($match->name));
            }

            foreach ($matches as $match) {
                $nameValue = htmlspecialchars($match->name);
                $namePart = 'name="' . str_pad($nameValue . '"', $maxName + 1);

                if ($match->conditions->count() > 0) {
                    if ($match->type) {
                        $xml[] = '    <match ' . $namePart . ' type="' . htmlspecialchars($match->type) . '">';
                    } else {
                        $xml[] = '    <match ' . $namePart . '>';
                    }

                    // Calculate max widths for conditions
                    $maxField = 0;
                    $maxOp = 0;
                    foreach ($match->conditions as $cond) {
                        $maxField = max($maxField, strlen($cond->field));
                        $maxOp = max($maxOp, strlen($cond->operator));
                    }

                    foreach ($match->conditions as $condition) {
                        $fieldValue = htmlspecialchars($condition->field);
                        $opValue = htmlspecialchars($condition->operator);

                        $fieldPart = 'field="' . str_pad($fieldValue . '"', $maxField + 1);
                        $opPart = 'operator="' . str_pad($opValue . '"', $maxOp + 1);

                        if ($condition->value) {
                            $xml[] = '      <condition ' . $fieldPart . ' ' . $opPart . ' value="' . htmlspecialchars($condition->value) . '"/>';
                        } else {
                            $xml[] = '      <condition ' . $fieldPart . ' ' . $opPart . '/>';
                        }
                    }

                    $xml[] = '    </match>';
                } else {
                    if ($match->type) {
                        $xml[] = '    <match ' . $namePart . ' type="' . htmlspecialchars($match->type) . '"/>';
                    } else {
                        $xml[] = '    <match ' . $namePart . '/>';
                    }
                }
            }

            $xml[] = '  </matches>';
        }

        // Export Rules with aligned attributes
        if ($rules->count() > 0) {
            $xml[] = '  <rules>';

            // Calculate max widths
            $maxName = 0;
            $maxClass = 0;
            foreach ($rules as $rule) {
                $maxName = max($maxName, strlen($rule->name));
                $maxClass = max($maxClass, strlen($rule->class));
            }

            foreach ($rules as $rule) {
                $nameValue = htmlspecialchars($rule->name);
                $classValue = htmlspecialchars($rule->class);

                $namePart = 'name="' . str_pad($nameValue . '"', $maxName + 1);
                $classPart = 'class="' . str_pad($classValue . '"', $maxClass + 1);
                $typePart = 'type="' . htmlspecialchars($rule->type) . '"';

                $attrs = [$namePart, $classPart, $typePart];

                if ($rule->delta) {
                    $attrs[] = 'delta="' . htmlspecialchars($rule->delta->name) . '"';
                }
                if ($rule->on_failure) {
                    $attrs[] = 'on_failure="' . htmlspecialchars($rule->on_failure) . '"';
                }

                $xml[] = '    <rule ' . implode(' ', $attrs) . '/>';
            }

            $xml[] = '  </rules>';
        }

        // Export Deltas with aligned attributes
        if ($deltas->count() > 0) {
            $xml[] = '  <deltas>';

            // Calculate max width
            $maxName = 0;
            foreach ($deltas as $delta) {
                $maxName = max($maxName, strlen($delta->name));
            }

            foreach ($deltas as $delta) {
                $nameValue = htmlspecialchars($delta->name);
                $namePart = 'name="' . str_pad($nameValue . '"', $maxName + 1);

                if ($delta->next) {
                    $xml[] = '    <delta ' . $namePart . ' next="' . htmlspecialchars($delta->next) . '"/>';
                } else {
                    $xml[] = '    <delta ' . $namePart . '/>';
                }
            }

            $xml[] = '  </deltas>';
        }

        $xml[] = '</routing>';

        return implode("\n", $xml);
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
