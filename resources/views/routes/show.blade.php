@extends('layouts.app')

@section('title', 'Route #' . $route->id . ' - LRMP')
@section('page-title', 'Route Details')
@section('page-description', 'Priority: ' . $route->priority)

@section('page-actions')
    <a href="{{ route('routes.edit', $route->id) }}" class="btn btn-warning">
        <i class="bi bi-pencil"></i> Edit
    </a>
    <a href="{{ route('routes.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0"><i class="bi bi-signpost-split"></i> Route Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Priority:</strong>
                    </div>
                    <div class="col-md-9">
                        <span class="badge bg-secondary">{{ $route->priority }}</span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Route File:</strong>
                    </div>
                    <div class="col-md-9">
                        @if($route->routeFile)
                            <a href="{{ route('route-files.show', $route->routefile_id) }}">
                                <i class="bi bi-file-earmark-code"></i> {{ $route->routeFile->name }}
                            </a>
                            <br>
                            <small class="text-muted">File: <code>{{ $route->routeFile->file_name }}</code></small>
                        @else
                            <span class="text-muted">No file assigned</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Service:</strong>
                    </div>
                    <div class="col-md-9">
                        @if($route->service)
                            <a href="{{ route('services.show', $route->service_id) }}">
                                <i class="bi bi-gear"></i> {{ $route->service->name }}
                            </a>
                        @else
                            <span class="text-muted">No service assigned</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Match:</strong>
                    </div>
                    <div class="col-md-9">
                        @if($route->match)
                            <a href="{{ route('matches.show', $route->match_id) }}">
                                <i class="bi bi-filter"></i> {{ $route->match->name }}
                            </a>
                            @if($route->match->conditions && $route->match->conditions->count() > 0)
                                <span class="badge bg-primary ms-2">{{ $route->match->conditions->count() }} conditions</span>
                            @endif
                        @else
                            <span class="text-muted">No match condition</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Rule:</strong>
                    </div>
                    <div class="col-md-9">
                        @if($route->rule)
                            <a href="{{ route('rules.show', $route->rule_id) }}">
                                <i class="bi bi-card-checklist"></i> {{ $route->rule->name }}
                            </a>
                            <br>
                            <small class="text-muted">Class: <code>{{ $route->rule->class }}</code></small>
                        @else
                            <span class="text-muted">No rule assigned</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Chain Class:</strong>
                    </div>
                    <div class="col-md-9">
                        @if($route->chainclass)
                            <code>{{ $route->chainclass }}</code>
                        @else
                            <span class="text-muted">Not specified</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Type:</strong>
                    </div>
                    <div class="col-md-9">
                        @if($route->type)
                            <span class="badge bg-info badge-type">{{ $route->type }}</span>
                        @else
                            <span class="text-muted">Not specified</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Created:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $route->created_at->format('M d, Y H:i') }}
                        <small class="text-muted">({{ $route->created_at->diffForHumans() }})</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <strong>Last Updated:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $route->updated_at->format('M d, Y H:i') }}
                        <small class="text-muted">({{ $route->updated_at->diffForHumans() }})</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0"><i class="bi bi-diagram-3"></i> Route Flow Diagram</h5>
            </div>
            <div class="card-body">
                <div id="route-network" style="height: 400px; border: 1px solid #dee2e6; border-radius: 0.375rem;"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-info mb-3">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0"><i class="bi bi-info-circle"></i> Quick Links</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($route->routeFile)
                    <a href="{{ route('route-files.show', $route->routefile_id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-file-earmark-code"></i> View Route File
                    </a>
                    @endif
                    @if($route->service)
                    <a href="{{ route('services.show', $route->service_id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-gear"></i> View Service
                    </a>
                    @endif
                    @if($route->match)
                    <a href="{{ route('matches.show', $route->match_id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-filter"></i> View Match
                    </a>
                    @endif
                    @if($route->rule)
                    <a href="{{ route('rules.show', $route->rule_id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-card-checklist"></i> View Rule
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0"><i class="bi bi-exclamation-triangle"></i> Danger Zone</h5>
            </div>
            <div class="card-body">
                <p class="card-text">Deleting this route cannot be undone.</p>
                <button type="button" class="btn btn-danger w-100" onclick="confirmDelete()">
                    <i class="bi bi-trash"></i> Delete Route
                </button>
                <form id="delete-form"
                      action="{{ route('routes.destroy', $route->id) }}"
                      method="POST"
                      class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete this route?')) {
        document.getElementById('delete-form').submit();
    }
}

// Create route flow diagram with vis.js
document.addEventListener('DOMContentLoaded', function() {
    const nodes = new vis.DataSet([
        { id: 1, label: 'Route File\n{{ $route->routeFile->name ?? "N/A" }}', color: '#667eea', shape: 'box' },
        { id: 2, label: 'Service\n{{ $route->service->name ?? "N/A" }}', color: '#f093fb', shape: 'ellipse' },
        { id: 3, label: 'Match\n{{ $route->match->name ?? "None" }}', color: '#4facfe', shape: 'diamond' },
        { id: 4, label: 'Rule\n{{ $route->rule->name ?? "None" }}', color: '#43e97b', shape: 'box' },
        { id: 5, label: 'Output\nPriority: {{ $route->priority }}', color: '#fa709a', shape: 'ellipse' }
    ]);

    const edges = new vis.DataSet([
        { from: 1, to: 2, arrows: 'to', label: 'contains' },
        { from: 2, to: 3, arrows: 'to', label: 'uses' },
        { from: 3, to: 4, arrows: 'to', label: 'applies' },
        { from: 4, to: 5, arrows: 'to', label: 'produces' }
    ]);

    const container = document.getElementById('route-network');
    const data = { nodes: nodes, edges: edges };
    const options = {
        nodes: {
            font: {
                color: '#ffffff',
                size: 14,
                face: 'Arial'
            },
            borderWidth: 2,
            borderWidthSelected: 3
        },
        edges: {
            width: 2,
            color: { color: '#848484', highlight: '#667eea' },
            font: { size: 12, align: 'middle' },
            smooth: { type: 'cubicBezier' }
        },
        physics: {
            enabled: true,
            stabilization: {
                iterations: 100
            },
            barnesHut: {
                gravitationalConstant: -2000,
                springConstant: 0.04,
                springLength: 150
            }
        },
        layout: {
            hierarchical: {
                direction: 'LR',
                sortMethod: 'directed',
                levelSeparation: 200,
                nodeSpacing: 150
            }
        }
    };

    const network = new vis.Network(container, data, options);
});
</script>
@endpush
