@extends('layouts.app')

@section('title', $routeFile->name . ' - LRMP')
@section('page-title', $routeFile->name)
@section('page-description', 'Route File Details')

@section('page-actions')
    <a href="{{ route('route-files.wizard', $routeFile->id) }}" class="btn btn-primary">
        <i class="bi bi-gear"></i> Setup Wizard
    </a>
    <a href="{{ route('route-files.edit', $routeFile->id) }}" class="btn btn-outline-warning">
        <i class="bi bi-pencil"></i> Edit Info
    </a>
    <a href="{{ route('route-files.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0"><i class="bi bi-file-earmark-code"></i> File Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Name:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $routeFile->name }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>File Name:</strong>
                    </div>
                    <div class="col-md-9">
                        <code class="text-primary">{{ $routeFile->file_name }}</code>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Project:</strong>
                    </div>
                    <div class="col-md-9">
                        @if($routeFile->project)
                            <a href="{{ route('projects.show', $routeFile->project_id) }}">
                                <i class="bi bi-folder"></i> {{ $routeFile->project->name }}
                            </a>
                        @else
                            <span class="text-muted">No project assigned</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Description:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $routeFile->description ?: 'No description provided' }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Created:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $routeFile->created_at->format('M d, Y H:i') }}
                        <small class="text-muted">({{ $routeFile->created_at->diffForHumans() }})</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <strong>Last Updated:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $routeFile->updated_at->format('M d, Y H:i') }}
                        <small class="text-muted">({{ $routeFile->updated_at->diffForHumans() }})</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="bi bi-signpost-split"></i> Routes in This File</h5>
                <span class="badge bg-primary">{{ $routeFile->routes->count() }}</span>
            </div>
            <div class="card-body">
                @if($routeFile->routes->count() > 0)
                    @php
                        $routesByService = $routeFile->routes->groupBy('from_service_id');
                    @endphp

                    <div class="row">
                        <!-- Vertical Tab Navigation -->
                        <div class="col-md-3">
                            <ul class="nav nav-pills flex-column" id="serviceTabs" role="tablist">
                                @foreach($routesByService as $serviceId => $routes)
                                    @php
                                        $service = $routes->first()->service;
                                        $isFirst = $loop->first;
                                    @endphp
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $isFirst ? 'active' : '' }} w-100 text-start"
                                                id="service-{{ $serviceId }}-tab"
                                                data-bs-toggle="tab"
                                                data-bs-target="#service-{{ $serviceId }}"
                                                type="button"
                                                role="tab"
                                                aria-controls="service-{{ $serviceId }}"
                                                aria-selected="{{ $isFirst ? 'true' : 'false' }}">
                                            <i class="bi bi-gear"></i> {{ $service->name ?? 'Unknown' }}
                                            <span class="badge bg-secondary float-end">{{ $routes->count() }}</span>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Tab Content -->
                        <div class="col-md-9">
                            <div class="tab-content" id="serviceTabsContent">
                                @foreach($routesByService as $serviceId => $routes)
                                    @php
                                        $service = $routes->first()->service;
                                        $isFirst = $loop->first;
                                    @endphp
                                    <div class="tab-pane fade {{ $isFirst ? 'show active' : '' }}"
                                         id="service-{{ $serviceId }}"
                                         role="tabpanel"
                                         aria-labelledby="service-{{ $serviceId }}-tab">
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
                    <div class="text-center py-4">
                        <i class="bi bi-signpost-split fs-2 text-muted d-block mb-2"></i>
                        <p class="text-muted mb-3">No routes in this file yet</p>
                        <a href="{{ route('routes.create', ['routefile_id' => $routeFile->id]) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-circle"></i> Add First Route
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0"><i class="bi bi-exclamation-triangle"></i> Danger Zone</h5>
            </div>
            <div class="card-body">
                <p class="card-text">Deleting this route file cannot be undone. This will also delete {{ $routeFile->routes->count() }} route(s).</p>
                <button type="button" class="btn btn-danger w-100" onclick="confirmDelete()">
                    <i class="bi bi-trash"></i> Delete Route File
                </button>
                <form id="delete-form"
                      action="{{ route('route-files.destroy', $routeFile->id) }}"
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

@push('styles')
<style>
    /* Improve drag handle visibility */
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
    /* Vertical nav pills styling */
    .nav-pills .nav-link {
        margin-bottom: 0.5rem;
        border-radius: 0.375rem;
        transition: all 0.2s;
    }
    .nav-pills .nav-link:hover {
        background-color: #e9ecef;
    }
    .nav-pills .nav-link.active {
        font-weight: 600;
    }
</style>
@endpush

@push('scripts')
<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete this route file? This will also delete {{ $routeFile->routes->count() }} route(s).')) {
        document.getElementById('delete-form').submit();
    }
}

// Initialize drag and drop sorting for each service group
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing sortable tables...');

    // Initialize sortable for all service groups
    const sortableTables = document.querySelectorAll('.sortable-routes');
    console.log('Found ' + sortableTables.length + ' sortable tables');

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
    const tabs = document.querySelectorAll('#serviceTabs button[data-bs-toggle="tab"]');
    console.log('Found ' + tabs.length + ' tabs');

    tabs.forEach(function(tab) {
        tab.addEventListener('shown.bs.tab', function(event) {
            console.log('Tab switched to: ' + event.target.textContent);
        });
    });
});
</script>
@endpush
