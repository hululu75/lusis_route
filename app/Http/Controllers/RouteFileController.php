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
     * Generate XML preview for route file
     */
    private function generateXmlPreview(RouteFile $routeFile)
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><routing></routing>');

        // Export Routes (First)
        $routesNode = $xml->addChild('routes');

        // Group routes by service
        $routesByService = $routeFile->routes->groupBy('from_service_id');

        foreach ($routesByService as $serviceId => $routes) {
            $service = $routes->first()->service;
            if (!$service) continue;

            $routeNode = $routesNode->addChild('route');
            $routeNode->addAttribute('class', $service->name);

            foreach ($routes->sortBy('priority') as $route) {
                $caseNode = $routeNode->addChild('case');

                if ($route->match) {
                    $caseNode->addAttribute('cond', $route->match->name);
                }
                if ($route->rule) {
                    $caseNode->addAttribute('rule', $route->rule->name);
                }
                if ($route->chainclass) {
                    $caseNode->addAttribute('chainclass', $route->chainclass);
                }
            }
        }

        // Collect unique matches
        $matches = collect();
        foreach ($routeFile->routes as $route) {
            if ($route->match) {
                $matches->push($route->match);
            }
        }
        $matches = $matches->unique('id');

        // Export Matches (Second)
        if ($matches->count() > 0) {
            $matchesNode = $xml->addChild('matches');
            foreach ($matches as $match) {
                $matchNode = $matchesNode->addChild('match');
                $matchNode->addAttribute('name', $match->name);
                if ($match->type) {
                    $matchNode->addAttribute('type', $match->type);
                }

                // Export conditions
                foreach ($match->conditions as $condition) {
                    $condNode = $matchNode->addChild('condition');
                    $condNode->addAttribute('field', $condition->field);
                    $condNode->addAttribute('operator', $condition->operator);
                    if ($condition->value) {
                        $condNode->addAttribute('value', $condition->value);
                    }
                }
            }
        }

        // Collect unique rules
        $rules = collect();
        foreach ($routeFile->routes as $route) {
            if ($route->rule) {
                $rules->push($route->rule);
            }
        }
        $rules = $rules->unique('id');

        // Export Rules (Third)
        if ($rules->count() > 0) {
            $rulesNode = $xml->addChild('rules');
            foreach ($rules as $rule) {
                $ruleNode = $rulesNode->addChild('rule');
                $ruleNode->addAttribute('name', $rule->name);
                $ruleNode->addAttribute('class', $rule->class);
                $ruleNode->addAttribute('type', $rule->type);
                if ($rule->delta) {
                    $ruleNode->addAttribute('delta', $rule->delta->name);
                }
                if ($rule->on_failure) {
                    $ruleNode->addAttribute('on_failure', $rule->on_failure);
                }
            }
        }

        // Collect unique deltas
        $deltas = collect();
        foreach ($routeFile->routes as $route) {
            if ($route->rule && $route->rule->delta) {
                $deltas->push($route->rule->delta);
            }
        }
        $deltas = $deltas->unique('id');

        // Export Deltas (Fourth)
        if ($deltas->count() > 0) {
            $deltasNode = $xml->addChild('deltas');
            foreach ($deltas as $delta) {
                $deltaNode = $deltasNode->addChild('delta');
                $deltaNode->addAttribute('name', $delta->name);
                if ($delta->next) {
                    $deltaNode->addAttribute('next', $delta->next);
                }
            }
        }

        // Format XML with proper indentation
        $dom = new \DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());

        return $dom->saveXML();
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
