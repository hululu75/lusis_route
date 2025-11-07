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
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createServiceModal">
                        <i class="bi bi-plus-circle"></i> Create Service
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#viewServicesModal">
                        <i class="bi bi-list"></i> View All Services
                    </button>
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
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createMatchModal">
                        <i class="bi bi-plus-circle"></i> Create Match
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#viewMatchesModal">
                        <i class="bi bi-list"></i> View All Matches
                    </button>
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
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDeltaModal">
                        <i class="bi bi-plus-circle"></i> Create Delta
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#viewDeltasModal">
                        <i class="bi bi-list"></i> View All Deltas
                    </button>
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
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRuleModal" {{ $stats['services'] == 0 ? 'disabled' : '' }}>
                        <i class="bi bi-plus-circle"></i> Create Rule
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#viewRulesModal">
                        <i class="bi bi-list"></i> View All Rules
                    </button>
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
                <p class="card-text mb-3">
                    <i class="bi bi-signpost-split"></i> Create routes to connect services, matches, and rules together.
                </p>

                @if($stats['services'] == 0 || $stats['rules'] == 0)
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> You need at least one service and one rule before creating routes.
                    Please complete steps 1 and 4 first.
                </div>
                @else
                <!-- Quick Route Creation Form -->
                <div class="card bg-light mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-plus-circle"></i> Quick Create Route</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('routes.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="routefile_id" value="{{ $routeFile->id }}">

                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="from_service_id" class="form-label">
                                        From Service <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('from_service_id') is-invalid @enderror"
                                            id="from_service_id"
                                            name="from_service_id"
                                            required>
                                        <option value="">-- Select Service --</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('from_service_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="match_id" class="form-label">
                                        Match Condition
                                    </label>
                                    <select class="form-select @error('match_id') is-invalid @enderror"
                                            id="match_id"
                                            name="match_id">
                                        <option value="">-- No Condition --</option>
                                        @foreach($matches as $match)
                                            <option value="{{ $match->id }}">
                                                {{ $match->name }}
                                                @if($match->type) ({{ $match->type }}) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('match_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="rule_id" class="form-label">
                                        Rule
                                    </label>
                                    <select class="form-select @error('rule_id') is-invalid @enderror"
                                            id="rule_id"
                                            name="rule_id">
                                        <option value="">-- No Rule --</option>
                                        @foreach($rules as $rule)
                                            <option value="{{ $rule->id }}">
                                                {{ $rule->name }} → {{ $rule->class }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('rule_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="chainclass" class="form-label">Chain Class</label>
                                    <input type="text"
                                           class="form-control @error('chainclass') is-invalid @enderror"
                                           id="chainclass"
                                           name="chainclass"
                                           value="{{ old('chainclass') }}"
                                           placeholder="Optional">
                                    @error('chainclass')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Create Route
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif

                <!-- Existing Routes List with Service Tabs -->
                @if($routeFile->routes->count() > 0)
                <div class="card">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-list"></i> Manage Routes ({{ $routeFile->routes->count() }})</h6>
                    </div>
                    <div class="card-body">
                        @php
                            $routesByService = $routeFile->routes->groupBy('from_service_id');
                        @endphp

                        <!-- Tab Navigation -->
                        <ul class="nav nav-tabs" id="wizardServiceTabs" role="tablist">
                            @foreach($routesByService as $serviceId => $routes)
                                @php
                                    $service = $routes->first()->service;
                                    $isFirst = $loop->first;
                                @endphp
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $isFirst ? 'active' : '' }}"
                                            id="wizard-service-{{ $serviceId }}-tab"
                                            data-bs-toggle="tab"
                                            data-bs-target="#wizard-service-{{ $serviceId }}"
                                            type="button"
                                            role="tab"
                                            aria-controls="wizard-service-{{ $serviceId }}"
                                            aria-selected="{{ $isFirst ? 'true' : 'false' }}">
                                        <i class="bi bi-gear"></i> {{ $service->name ?? 'Unknown' }}
                                        <span class="badge bg-secondary ms-1">{{ $routes->count() }}</span>
                                    </button>
                                </li>
                            @endforeach
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content mt-3" id="wizardServiceTabsContent">
                            @foreach($routesByService as $serviceId => $routes)
                                @php
                                    $service = $routes->first()->service;
                                    $isFirst = $loop->first;
                                @endphp
                                <div class="tab-pane fade {{ $isFirst ? 'show active' : '' }}"
                                     id="wizard-service-{{ $serviceId }}"
                                     role="tabpanel"
                                     aria-labelledby="wizard-service-{{ $serviceId }}-tab">
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle"></i> Drag rows by the <i class="bi bi-grip-vertical"></i> handle to reorder priorities
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="50"><i class="bi bi-grip-vertical"></i></th>
                                                    <th>Match</th>
                                                    <th>Rule</th>
                                                    <th>Chain Class</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="sortable-routes" data-service-id="{{ $serviceId }}">
                                                @foreach($routes->sortBy('priority') as $route)
                                                <tr class="sortable-row" data-route-id="{{ $route->id }}">
                                                    <td class="text-center drag-handle">
                                                        <i class="bi bi-grip-vertical text-muted"></i>
                                                    </td>
                                                    <td>{{ $route->match->name ?? '-' }}</td>
                                                    <td>{{ $route->rule->name ?? '-' }}</td>
                                                    <td>
                                                        @if($route->chainclass)
                                                            <code class="small">{{ Str::limit($route->chainclass, 20) }}</code>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('routes.show', $route->id) }}"
                                                           class="btn btn-sm btn-outline-primary"
                                                           title="View Route">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('routes.edit', $route->id) }}"
                                                           class="btn btn-sm btn-outline-warning"
                                                           title="Edit Route">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <p>No routes created yet. Use the form above to create your first route.</p>
                </div>
                @endif
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

<!-- Modals -->
<!-- Create Service Modal -->
<div class="modal fade" id="createServiceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Create Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <iframe src="{{ route('services.create') }}" style="width:100%; height:500px; border:none;" id="createServiceFrame"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- View Services Modal -->
<div class="modal fade" id="viewServicesModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-list"></i> All Services</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <iframe src="{{ route('services.index') }}" style="width:100%; height:600px; border:none;" id="viewServicesFrame"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- Create Match Modal -->
<div class="modal fade" id="createMatchModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Create Match</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <iframe src="{{ route('matches.create') }}" style="width:100%; height:500px; border:none;" id="createMatchFrame"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- View Matches Modal -->
<div class="modal fade" id="viewMatchesModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-list"></i> All Matches</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <iframe src="{{ route('matches.index') }}" style="width:100%; height:600px; border:none;" id="viewMatchesFrame"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- Create Delta Modal -->
<div class="modal fade" id="createDeltaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Create Delta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <iframe src="{{ route('deltas.create') }}" style="width:100%; height:500px; border:none;" id="createDeltaFrame"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- View Deltas Modal -->
<div class="modal fade" id="viewDeltasModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-list"></i> All Deltas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <iframe src="{{ route('deltas.index') }}" style="width:100%; height:600px; border:none;" id="viewDeltasFrame"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- Create Rule Modal -->
<div class="modal fade" id="createRuleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Create Rule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <iframe src="{{ route('rules.create') }}" style="width:100%; height:500px; border:none;" id="createRuleFrame"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- View Rules Modal -->
<div class="modal fade" id="viewRulesModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-list"></i> All Rules</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <iframe src="{{ route('rules.index') }}" style="width:100%; height:600px; border:none;" id="viewRulesFrame"></iframe>
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

.modal-body iframe {
    border-radius: 0.375rem;
}

/* Drag and drop styles */
.drag-handle {
    cursor: grab !important;
}
.drag-handle:active {
    cursor: grabbing !important;
}
.sortable-row .bi-grip-vertical {
    cursor: grab !important;
    font-size: 1.2rem;
    transition: color 0.2s;
}
.sortable-row:hover .bi-grip-vertical {
    color: #0d6efd !important;
}
.sortable-row .bi-grip-vertical:active {
    cursor: grabbing !important;
}
.sortable-row {
    transition: background-color 0.2s;
}
.sortable-row:hover {
    background-color: #f8f9fa;
}
.sortable-ghost {
    opacity: 0.5;
    background: #e3f2fd !important;
}
.sortable-chosen {
    background: #e7f3ff !important;
}
.nav-tabs .nav-link {
    color: #495057;
}
.nav-tabs .nav-link.active {
    font-weight: 600;
}
</style>
@endpush

@push('scripts')
<script>
// Reload page when modal is closed to refresh stats and dropdowns
document.addEventListener('DOMContentLoaded', function() {
    // Listen to all modal hide events
    const modals = document.querySelectorAll('.modal');
    modals.forEach(function(modal) {
        modal.addEventListener('hidden.bs.modal', function() {
            // Check if modal contains a create form (modals with "create" in their ID)
            if (this.id.includes('create') || this.id.includes('view')) {
                // Reload the page to refresh stats and dropdown options
                location.reload();
            }
        });
    });

    // Reload iframe content when modal is shown (to get fresh content)
    modals.forEach(function(modal) {
        modal.addEventListener('shown.bs.modal', function() {
            const iframe = this.querySelector('iframe');
            if (iframe && iframe.src) {
                // Reload iframe to get fresh content
                iframe.src = iframe.src;
            }
        });
    });

    // Initialize drag and drop sorting for route tables
    const sortableTables = document.querySelectorAll('.sortable-routes');
    console.log('Found ' + sortableTables.length + ' sortable route tables');

    sortableTables.forEach(function(tbody, index) {
        console.log('Initializing sortable ' + index + ' for service: ' + tbody.dataset.serviceId);

        const sortable = Sortable.create(tbody, {
            animation: 150,
            handle: '.bi-grip-vertical',
            ghostClass: 'sortable-ghost',
            forceFallback: false,
            onStart: function(evt) {
                console.log('Drag started');
            },
            onEnd: function(evt) {
                console.log('Drag ended, updating priorities...');

                // Get all rows in this service group
                const rows = tbody.querySelectorAll('.sortable-row');
                const routeIds = [];

                // Collect route IDs in new order
                rows.forEach((row, index) => {
                    routeIds.push(row.dataset.routeId);
                });

                console.log('New order:', routeIds);

                // Send AJAX request to update priorities
                fetch('{{ route("routes.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ ids: routeIds })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Routes reordered successfully');
                        // Show a subtle success indicator
                        const alert = document.createElement('div');
                        alert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3';
                        alert.style.zIndex = '9999';
                        alert.innerHTML = '<i class="bi bi-check-circle"></i> Order saved successfully! <button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                        document.body.appendChild(alert);
                        setTimeout(() => alert.remove(), 3000);
                    }
                })
                .catch(error => {
                    console.error('Error reordering routes:', error);
                    alert('Failed to save new order. Please refresh the page.');
                });
            }
        });
    });

    // Log tab initialization
    const tabs = document.querySelectorAll('#wizardServiceTabs button[data-bs-toggle="tab"]');
    console.log('Found ' + tabs.length + ' service tabs');

    tabs.forEach(function(tab) {
        tab.addEventListener('shown.bs.tab', function(event) {
            console.log('Tab switched to: ' + event.target.textContent);
        });
    });
});
</script>
@endpush
