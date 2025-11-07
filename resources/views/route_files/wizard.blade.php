@extends('layouts.app')

@section('title', 'Setup Wizard - ' . $routeFile->name)
@section('page-title', 'Route File Setup Wizard')
@section('page-description', $routeFile->name . ' - ' . $routeFile->project->name)

@section('page-actions')
    <a href="{{ route('route-files.show', $routeFile->id) }}" class="btn btn-outline-secondary">
        <i class="bi bi-x-circle"></i> Skip Wizard
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Success Alert -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Welcome Card -->
        <div class="card mb-4 border-primary">
            <div class="card-body">
                <h4 class="card-title"><i class="bi bi-magic"></i> Welcome to the Setup Wizard!</h4>
                <p class="card-text mb-0">
                    Let's set up your routing configuration step by step. This wizard will help you create:
                </p>
                <ul class="mt-2 mb-0">
                    <li><strong>Services</strong> - Define the endpoints/services in your routing table</li>
                    <li><strong>Matches</strong> - Create conditions to match incoming messages</li>
                    <li><strong>Deltas</strong> - Define message transformations (optional)</li>
                    <li><strong>Rules</strong> - Set up routing rules connecting services and deltas</li>
                    <li><strong>Routes</strong> - Build the actual route entries</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Setup Steps -->
<div class="row">
    <!-- Step 1: Services -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100 {{ $stats['services'] > 0 ? 'border-success' : '' }}">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <span class="badge bg-primary me-2">1</span>
                    Services
                </h5>
                @if($stats['services'] > 0)
                <span class="badge bg-success">{{ $stats['services'] }} created</span>
                @else
                <span class="badge bg-secondary">Not started</span>
                @endif
            </div>
            <div class="card-body">
                <p class="card-text">
                    <i class="bi bi-gear"></i> Services represent the different endpoints or classes in your routing configuration.
                </p>
                @if($stats['services'] > 0)
                <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i> You have {{ $stats['services'] }} service(s) in this project.
                </div>
                @endif
                <div class="d-grid gap-2">
                    <a href="{{ route('services.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Create Service
                    </a>
                    <a href="{{ route('services.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-list"></i> View All Services
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 2: Matches -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100 {{ $stats['matches'] > 0 ? 'border-success' : '' }}">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <span class="badge bg-primary me-2">2</span>
                    Matches
                </h5>
                @if($stats['matches'] > 0)
                <span class="badge bg-success">{{ $stats['matches'] }} created</span>
                @else
                <span class="badge bg-secondary">Not started</span>
                @endif
            </div>
            <div class="card-body">
                <p class="card-text">
                    <i class="bi bi-filter"></i> Matches define conditions to filter and route messages based on their content.
                </p>
                @if($stats['matches'] > 0)
                <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i> You have {{ $stats['matches'] }} match(es) in this project.
                </div>
                @endif
                <div class="d-grid gap-2">
                    <a href="{{ route('matches.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Create Match
                    </a>
                    <a href="{{ route('matches.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-list"></i> View All Matches
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 3: Deltas (Optional) -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100 {{ $stats['deltas'] > 0 ? 'border-success' : '' }}">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <span class="badge bg-info me-2">3</span>
                    Deltas
                    <span class="badge bg-warning text-dark ms-1">Optional</span>
                </h5>
                @if($stats['deltas'] > 0)
                <span class="badge bg-success">{{ $stats['deltas'] }} created</span>
                @else
                <span class="badge bg-secondary">Not started</span>
                @endif
            </div>
            <div class="card-body">
                <p class="card-text">
                    <i class="bi bi-arrow-left-right"></i> Deltas define message transformations and field modifications.
                </p>
                @if($stats['deltas'] > 0)
                <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i> You have {{ $stats['deltas'] }} delta(s) in this project.
                </div>
                @else
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Deltas are optional. Skip if you don't need message transformations.
                </div>
                @endif
                <div class="d-grid gap-2">
                    <a href="{{ route('deltas.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Create Delta
                    </a>
                    <a href="{{ route('deltas.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-list"></i> View All Deltas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 4: Rules -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100 {{ $stats['rules'] > 0 ? 'border-success' : '' }}">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <span class="badge bg-primary me-2">4</span>
                    Rules
                </h5>
                @if($stats['rules'] > 0)
                <span class="badge bg-success">{{ $stats['rules'] }} created</span>
                @else
                <span class="badge bg-secondary">Not started</span>
                @endif
            </div>
            <div class="card-body">
                <p class="card-text">
                    <i class="bi bi-card-checklist"></i> Rules connect services with optional deltas to create routing actions.
                </p>
                @if($stats['rules'] > 0)
                <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i> You have {{ $stats['rules'] }} rule(s) in this project.
                </div>
                @else
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> Create services first before creating rules.
                </div>
                @endif
                <div class="d-grid gap-2">
                    <a href="{{ route('rules.create') }}" class="btn btn-primary" {{ $stats['services'] == 0 ? 'onclick="alert(\'Please create services first\'); return false;"' : '' }}>
                        <i class="bi bi-plus-circle"></i> Create Rule
                    </a>
                    <a href="{{ route('rules.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-list"></i> View All Rules
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 5: Routes -->
    <div class="col-lg-12 mb-4">
        <div class="card {{ $stats['routes'] > 0 ? 'border-success' : 'border-primary' }}">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <span class="badge bg-primary me-2">5</span>
                    Routes
                </h5>
                @if($stats['routes'] > 0)
                <span class="badge bg-success">{{ $stats['routes'] }} created</span>
                @else
                <span class="badge bg-secondary">Not started</span>
                @endif
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-8">
                        <p class="card-text">
                            <i class="bi bi-signpost-split"></i> Now create the actual routes for this route file. Routes connect services, matches, and rules together.
                        </p>
                        @if($stats['routes'] > 0)
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i> You have {{ $stats['routes'] }} route(s) in this route file.
                        </div>
                        @elseif($stats['services'] == 0 || $stats['rules'] == 0)
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> Create services and rules before creating routes.
                        </div>
                        @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Ready to create routes! You have all the required components.
                        </div>
                        @endif
                    </div>
                    <div class="col-lg-4">
                        <div class="d-grid gap-2">
                            <a href="{{ route('routes.create') }}" class="btn btn-lg btn-primary" {{ ($stats['services'] == 0 || $stats['rules'] == 0) ? 'onclick="alert(\'Please create services and rules first\'); return false;"' : '' }}>
                                <i class="bi bi-plus-circle"></i> Create Route
                            </a>
                            <a href="{{ route('routes.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-list"></i> View All Routes
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-lightning-charge"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <h6><i class="bi bi-upload"></i> Import XML</h6>
                        <p class="small text-muted">Already have an XML routing file? Import it to quickly set up everything.</p>
                        <a href="{{ route('xml.import') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-file-earmark-arrow-up"></i> Import XML File
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h6><i class="bi bi-file-earmark-code"></i> View Route File</h6>
                        <p class="small text-muted">View the route file details and all associated routes.</p>
                        <a href="{{ route('route-files.show', $routeFile->id) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-eye"></i> View Route File
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h6><i class="bi bi-check-circle"></i> Finish Setup</h6>
                        <p class="small text-muted">Done with the setup? Return to the route files list.</p>
                        <a href="{{ route('route-files.index') }}" class="btn btn-success btn-sm">
                            <i class="bi bi-check-circle"></i> Finish & Close Wizard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Progress Summary -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5><i class="bi bi-graph-up"></i> Setup Progress</h5>
                <div class="progress" style="height: 30px;">
                    @php
                        $progress = 0;
                        if($stats['services'] > 0) $progress += 25;
                        if($stats['matches'] > 0) $progress += 20;
                        if($stats['rules'] > 0) $progress += 30;
                        if($stats['routes'] > 0) $progress += 25;
                    @endphp
                    <div class="progress-bar progress-bar-striped progress-bar-animated {{ $progress == 100 ? 'bg-success' : 'bg-primary' }}"
                         role="progressbar"
                         style="width: {{ $progress }}%"
                         aria-valuenow="{{ $progress }}"
                         aria-valuemin="0"
                         aria-valuemax="100">
                        {{ $progress }}% Complete
                    </div>
                </div>
                <div class="mt-3 text-center">
                    @if($progress == 100)
                    <span class="text-success">
                        <i class="bi bi-check-circle-fill fs-4"></i>
                        <strong>Setup Complete!</strong> Your route file is ready to use.
                    </span>
                    @else
                    <span class="text-muted">
                        Follow the steps above to complete your route file setup.
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.badge {
    font-size: 0.9rem;
}

.border-success {
    border-width: 2px !important;
}
</style>
@endpush
