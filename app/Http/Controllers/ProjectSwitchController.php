<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectSwitchController extends Controller
{
    /**
     * Switch to a different project context
     */
    public function switch(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);

        // Store current project in session
        $request->session()->put('current_project_id', $project->id);

        return redirect()->back()->with('success', "Switched to project: {$project->name}");
    }

    /**
     * Clear project context (show all data)
     */
    public function clear(Request $request)
    {
        $request->session()->forget('current_project_id');

        return redirect()->back()->with('success', 'Viewing all projects');
    }
}
