@extends('layouts.app')

@section('title', 'Dashboard - LRMP')
@section('page-title', 'Dashboard')
@section('page-description', 'Overview of your routing configuration')

@section('content')
<div class="row g-4 mb-4">
    <!-- Statistics Cards -->
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Projects</h6>
                        <h2 class="mb-0">{{ $stats['projects'] ?? 0 }}</h2>
                    </div>
                    <i class="bi bi-folder fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Services</h6>
                        <h2 class="mb-0">{{ $stats['services'] ?? 0 }}</h2>
                    </div>
                    <i class="bi bi-gear fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Route Files</h6>
                        <h2 class="mb-0">{{ $stats['route_files'] ?? 0 }}</h2>
                    </div>
                    <i class="bi bi-file-earmark-code fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Routes</h6>
                        <h2 class="mb-0">{{ $stats['routes'] ?? 0 }}</h2>
                    </div>
                    <i class="bi bi-signpost-split fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Projects -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-folder text-primary"></i> Recent Projects</h5>
            </div>
            <div class="card-body">
                @if(isset($recentProjects) && $recentProjects->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($recentProjects as $project)
                    <a href="{{ route('projects.show', $project->id) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $project->name }}</h6>
                                <small class="text-muted">{{ Str::limit($project->description, 50) }}</small>
                            </div>
                            <span class="badge bg-primary">{{ $project->routeFiles->count() }} files</span>
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <p class="text-muted text-center py-3">
                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                    No projects yet. <a href="{{ route('projects.create') }}">Create your first project</a>
                </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-lightning text-warning"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('projects.create') }}" class="btn btn-outline-primary text-start">
                        <i class="bi bi-plus-circle me-2"></i> Create New Project
                    </a>
                    <a href="{{ route('services.create') }}" class="btn btn-outline-success text-start">
                        <i class="bi bi-plus-circle me-2"></i> Add New Service
                    </a>
                    <a href="{{ route('routes.create') }}" class="btn btn-outline-info text-start">
                        <i class="bi bi-plus-circle me-2"></i> Create New Route
                    </a>
                    <hr>
                    <a href="#" class="btn btn-outline-secondary text-start">
                        <i class="bi bi-download me-2"></i> Import XML Configuration
                    </a>
                    <a href="#" class="btn btn-outline-secondary text-start">
                        <i class="bi bi-upload me-2"></i> Export XML Configuration
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <!-- System Information -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-info-circle text-info"></i> System Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Version:</strong></td>
                                <td>1.0</td>
                            </tr>
                            <tr>
                                <td><strong>Framework:</strong></td>
                                <td>Laravel 11</td>
                            </tr>
                            <tr>
                                <td><strong>Database:</strong></td>
                                <td>SQLite</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Total Matches:</strong></td>
                                <td>{{ $stats['matches'] ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Rules:</strong></td>
                                <td>{{ $stats['rules'] ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Deltas:</strong></td>
                                <td>{{ $stats['deltas'] ?? 0 }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
