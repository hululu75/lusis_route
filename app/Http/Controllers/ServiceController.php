<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Helpers\ProjectHelper;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Service::withCount('routes')->latest();
        ProjectHelper::scopeToCurrentProject($query);
        $services = $query->get();
        return view('services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Check if a project is selected
        if (!ProjectHelper::hasCurrentProject()) {
            return redirect()->route('services.index')
                ->with('error', 'Please select a project first');
        }

        return view('services.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:64|unique:services',
            'description' => 'nullable|string',
        ]);

        // Auto-assign current project
        $validated['project_id'] = ProjectHelper::getCurrentProjectId();

        if (!$validated['project_id']) {
            return redirect()->route('services.index')
                ->with('error', 'Please select a project first');
        }

        Service::create($validated);

        return redirect()->route('services.index')
            ->with('success', 'Service created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        $service->load('routes');
        return view('services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        return view('services.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:64|unique:services,name,' . $service->id,
            'description' => 'nullable|string',
        ]);

        $service->update($validated);

        return redirect()->route('services.index')
            ->with('success', 'Service updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->route('services.index')
            ->with('success', 'Service deleted successfully!');
    }
}
