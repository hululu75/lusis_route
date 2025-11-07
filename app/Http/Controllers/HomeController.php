<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Service;
use App\Models\RouteFile;
use App\Models\Route;
use App\Models\RouteMatch;
use App\Models\Rule;
use App\Models\Delta;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $stats = [
            'projects' => Project::count(),
            'services' => Service::count(),
            'route_files' => RouteFile::count(),
            'routes' => Route::count(),
            'matches' => RouteMatch::count(),
            'rules' => Rule::count(),
            'deltas' => Delta::count(),
        ];

        $recentProjects = Project::with('routeFiles')
            ->latest()
            ->take(5)
            ->get();

        return view('home', compact('stats', 'recentProjects'));
    }
}
