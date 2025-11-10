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
            'xml_file' => 'required|file|mimetypes:text/xml,application/xml',
            'route_file_name' => 'required|string|max:128',
        ]);

        try {
            DB::beginTransaction();

            $project = Project::findOrFail($request->project_id);
            $xmlContent = file_get_contents($request->file('xml_file')->getRealPath());

            // Handle XML files without declaration by adding a default one if missing
            $xmlContent = trim($xmlContent);
            if (!preg_match('/^<\?xml/i', $xmlContent)) {
                $xmlContent = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . $xmlContent;
            }

            // Parse XML with error suppression for malformed files
            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($xmlContent, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOWARNING);

            if ($xml === false) {
                $errors = libxml_get_errors();
                libxml_clear_errors();
                $errorMsg = 'Failed to parse XML file';
                if (!empty($errors)) {
                    $errorMsg .= ': ' . $errors[0]->message;
                }
                throw new \Exception($errorMsg);
            }

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

                    // Use updateOrCreate with project_id
                    $delta = Delta::updateOrCreate(
                        [
                            'name' => $deltaName,
                            'project_id' => $project->id
                        ],
                        [
                            'next' => isset($deltaXml['next']) ? (string)$deltaXml['next'] : null,
                            'definition' => $deltaXml->asXML(),
                            'description' => 'Imported from XML',
                        ]
                    );

                    if ($delta->wasRecentlyCreated) {
                        $stats['deltas']++;
                    }
                }
            }

            // Import Matches (support both <matches> and <matchs> tags)
            $matchesNode = isset($xml->matches) ? $xml->matches : (isset($xml->matchs) ? $xml->matchs : null);
            if ($matchesNode && isset($matchesNode->match)) {
                foreach ($matchesNode->match as $matchXml) {
                    $matchName = (string)$matchXml['name'];

                    // Use firstOrCreate with project_id
                    $match = RouteMatch::firstOrCreate(
                        [
                            'name' => $matchName,
                            'project_id' => $project->id
                        ],
                        [
                            'type' => isset($matchXml['type']) ? (string)$matchXml['type'] : null,
                            'description' => 'Imported from XML',
                        ]
                    );

                    if ($match->wasRecentlyCreated) {
                        $stats['matches']++;
                    }

                    // Always clear and re-import conditions to ensure they're up-to-date
                    $match->conditions()->delete();

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
                }
            }

            // Import Rules
            if (isset($xml->rules->rule)) {
                foreach ($xml->rules->rule as $ruleXml) {
                    $ruleName = (string)$ruleXml['name'];
                    $deltaName = isset($ruleXml['delta']) ? (string)$ruleXml['delta'] : null;
                    // Find delta within the same project
                    $delta = $deltaName ? Delta::where('name', $deltaName)->where('project_id', $project->id)->first() : null;

                    // Use updateOrCreate with project_id
                    $rule = Rule::updateOrCreate(
                        [
                            'name' => $ruleName,
                            'project_id' => $project->id
                        ],
                        [
                            'class' => (string)$ruleXml['class'],
                            'type' => isset($ruleXml['type']) ? (string)$ruleXml['type'] : 'REQ',
                            'delta_id' => $delta ? $delta->id : null,
                            'on_failure' => isset($ruleXml['on_failure']) ? (string)$ruleXml['on_failure'] : null,
                            'description' => 'Imported from XML',
                        ]
                    );

                    if ($rule->wasRecentlyCreated) {
                        $stats['rules']++;
                    }
                }
            }

            // Import Routes
            if (isset($xml->routes->route)) {
                $priority = 0;
                foreach ($xml->routes->route as $routeXml) {
                    $serviceName = (string)$routeXml['class'];

                    // Create or get service with project_id
                    $service = Service::firstOrCreate(
                        [
                            'name' => $serviceName,
                            'project_id' => $project->id
                        ],
                        [
                            'description' => 'Auto-created from XML import',
                        ]
                    );

                    if ($service->wasRecentlyCreated) {
                        $stats['services']++;
                    }

                    // Process cases (new format)
                    if (isset($routeXml->case)) {
                        foreach ($routeXml->case as $caseXml) {
                            $matchName = isset($caseXml['cond']) ? (string)$caseXml['cond'] : null;
                            $ruleName = isset($caseXml['rule']) ? (string)$caseXml['rule'] : null;
                            $chainclass = isset($caseXml['chainclass']) ? (string)$caseXml['chainclass'] : null;

                            // Find match and rule within the same project
                            $match = $matchName ? RouteMatch::where('name', $matchName)->where('project_id', $project->id)->first() : null;
                            $rule = $ruleName ? Rule::where('name', $ruleName)->where('project_id', $project->id)->first() : null;

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
                    }
                    // Support legacy format: <route class="X" rule="Y"/> (without <case> elements)
                    elseif (isset($routeXml['rule'])) {
                        $ruleName = (string)$routeXml['rule'];
                        $matchName = isset($routeXml['cond']) ? (string)$routeXml['cond'] : null;
                        $chainclass = isset($routeXml['chainclass']) ? (string)$routeXml['chainclass'] : null;

                        // Find match and rule within the same project
                        $match = $matchName ? RouteMatch::where('name', $matchName)->where('project_id', $project->id)->first() : null;
                        $rule = $ruleName ? Rule::where('name', $ruleName)->where('project_id', $project->id)->first() : null;

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
                    } else {
                        // Default route without cases or rule
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

        // Export Routes (First)
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
                if ($delta->definition) {
                    // Parse and include delta definition
                    $deltaNode->addChild('definition', htmlspecialchars($delta->definition));
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
