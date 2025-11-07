<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\RouteFile;
use App\Models\Service;
use App\Models\RouteMatch;
use App\Models\MatchCondition;
use App\Models\Delta;
use App\Models\Rule;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleXMLElement;

class XmlImportExportController extends Controller
{
    /**
     * Show the import form
     */
    public function showImportForm()
    {
        $projects = Project::all();
        return view('xml.import', compact('projects'));
    }

    /**
     * Import XML file
     */
    public function import(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'xml_file' => 'required|file|mimes:xml',
            'route_file_name' => 'required|string|max:128',
        ]);

        try {
            DB::beginTransaction();

            $project = Project::findOrFail($request->project_id);
            $xmlContent = file_get_contents($request->file('xml_file')->getRealPath());
            $xml = new SimpleXMLElement($xmlContent);

            // Create route file
            $routeFile = RouteFile::create([
                'project_id' => $project->id,
                'name' => $request->route_file_name,
                'file_name' => $request->file('xml_file')->getClientOriginalName(),
                'description' => 'Imported from XML on ' . now()->format('Y-m-d H:i:s'),
            ]);

            $stats = [
                'services' => 0,
                'matches' => 0,
                'deltas' => 0,
                'rules' => 0,
                'routes' => 0,
            ];

            // Import Deltas
            if (isset($xml->deltas->delta)) {
                foreach ($xml->deltas->delta as $deltaXml) {
                    $deltaName = (string)$deltaXml['name'];

                    if (!Delta::where('name', $deltaName)->exists()) {
                        Delta::create([
                            'name' => $deltaName,
                            'next' => isset($deltaXml['next']) ? (string)$deltaXml['next'] : null,
                            'definition' => $deltaXml->asXML(),
                            'description' => 'Imported from XML',
                        ]);
                        $stats['deltas']++;
                    }
                }
            }

            // Import Matches
            if (isset($xml->matches->match)) {
                foreach ($xml->matches->match as $matchXml) {
                    $matchName = (string)$matchXml['name'];

                    if (!RouteMatch::where('name', $matchName)->exists()) {
                        $match = RouteMatch::create([
                            'name' => $matchName,
                            'type' => isset($matchXml['type']) ? (string)$matchXml['type'] : null,
                            'description' => 'Imported from XML',
                        ]);

                        // Import conditions
                        if (isset($matchXml->condition)) {
                            foreach ($matchXml->condition as $conditionXml) {
                                MatchCondition::create([
                                    'match_id' => $match->id,
                                    'field' => (string)$conditionXml['field'],
                                    'operator' => (string)$conditionXml['operator'],
                                    'value' => isset($conditionXml['value']) ? (string)$conditionXml['value'] : null,
                                ]);
                            }
                        }
                        $stats['matches']++;
                    }
                }
            }

            // Import Rules
            if (isset($xml->rules->rule)) {
                foreach ($xml->rules->rule as $ruleXml) {
                    $ruleName = (string)$ruleXml['name'];

                    if (!Rule::where('name', $ruleName)->exists()) {
                        $deltaName = isset($ruleXml['delta']) ? (string)$ruleXml['delta'] : null;
                        $delta = $deltaName ? Delta::where('name', $deltaName)->first() : null;

                        Rule::create([
                            'name' => $ruleName,
                            'class' => (string)$ruleXml['class'],
                            'type' => isset($ruleXml['type']) ? (string)$ruleXml['type'] : 'REQ',
                            'delta_id' => $delta ? $delta->id : null,
                            'on_failure' => isset($ruleXml['on_failure']) ? (string)$ruleXml['on_failure'] : null,
                            'description' => 'Imported from XML',
                        ]);
                        $stats['rules']++;
                    }
                }
            }

            // Import Routes
            if (isset($xml->routes->route)) {
                $priority = 0;
                foreach ($xml->routes->route as $routeXml) {
                    $serviceName = (string)$routeXml['class'];

                    // Create or get service
                    $service = Service::firstOrCreate(
                        ['name' => $serviceName],
                        [
                            'type' => 'REQ',
                            'description' => 'Auto-created from XML import',
                        ]
                    );

                    if ($service->wasRecentlyCreated) {
                        $stats['services']++;
                    }

                    // Process cases
                    if (isset($routeXml->case)) {
                        foreach ($routeXml->case as $caseXml) {
                            $matchName = isset($caseXml['cond']) ? (string)$caseXml['cond'] : null;
                            $ruleName = isset($caseXml['rule']) ? (string)$caseXml['rule'] : null;
                            $chainclass = isset($caseXml['chainclass']) ? (string)$caseXml['chainclass'] : null;

                            $match = $matchName ? RouteMatch::where('name', $matchName)->first() : null;
                            $rule = $ruleName ? Rule::where('name', $ruleName)->first() : null;

                            Route::create([
                                'routefile_id' => $routeFile->id,
                                'from_service_id' => $service->id,
                                'match_id' => $match ? $match->id : null,
                                'rule_id' => $rule ? $rule->id : null,
                                'chainclass' => $chainclass,
                                'type' => null,
                                'priority' => $priority++,
                            ]);
                            $stats['routes']++;
                        }
                    } else {
                        // Default route without cases
                        Route::create([
                            'routefile_id' => $routeFile->id,
                            'from_service_id' => $service->id,
                            'match_id' => null,
                            'rule_id' => null,
                            'chainclass' => null,
                            'type' => null,
                            'priority' => $priority++,
                        ]);
                        $stats['routes']++;
                    }
                }
            }

            DB::commit();

            $message = sprintf(
                'XML imported successfully! Created: %d services, %d matches, %d deltas, %d rules, %d routes',
                $stats['services'],
                $stats['matches'],
                $stats['deltas'],
                $stats['rules'],
                $stats['routes']
            );

            return redirect()->route('route-files.show', $routeFile->id)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Show export form
     */
    public function showExportForm()
    {
        $projects = Project::with('routeFiles')->get();
        $routeFiles = RouteFile::with('project')->get();
        return view('xml.export', compact('projects', 'routeFiles'));
    }

    /**
     * Export route file to XML
     */
    public function export(Request $request)
    {
        $request->validate([
            'route_file_id' => 'required|exists:route_files,id',
        ]);

        $routeFile = RouteFile::with([
            'routes.service',
            'routes.match.conditions',
            'routes.rule.delta'
        ])->findOrFail($request->route_file_id);

        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><routing></routing>');

        // Collect unique deltas
        $deltas = collect();
        foreach ($routeFile->routes as $route) {
            if ($route->rule && $route->rule->delta) {
                $deltas->push($route->rule->delta);
            }
        }
        $deltas = $deltas->unique('id');

        // Export Deltas
        if ($deltas->count() > 0) {
            $deltasNode = $xml->addChild('deltas');
            foreach ($deltas as $delta) {
                $deltaNode = $deltasNode->addChild('delta');
                $deltaNode->addAttribute('name', $delta->name);
                if ($delta->next) {
                    $deltaNode->addAttribute('next', $delta->next);
                }
                if ($delta->definition) {
                    // Parse and include delta definition
                    $deltaNode->addChild('definition', htmlspecialchars($delta->definition));
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

        // Export Matches
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

        // Export Rules
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

        // Export Routes
        $routesNode = $xml->addChild('routes');

        // Group routes by service
        $routesByService = $routeFile->routes->groupBy('from_service_id');

        foreach ($routesByService as $serviceId => $routes) {
            $service = $routes->first()->service;
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

        // Format XML with proper indentation
        $dom = new \DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());
        $xmlString = $dom->saveXML();

        $fileName = 'routing_' . $routeFile->file_name . '_' . now()->format('YmdHis') . '.xml';

        return response($xmlString, 200, [
            'Content-Type' => 'application/xml',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
