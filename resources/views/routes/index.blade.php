@extends('layouts.app')

@section('title', 'Routes - LRMP')
@section('page-title', 'Routes')
@section('page-description', 'Manage routing configuration - Drag to reorder')

@section('page-actions')
    <a href="{{ route('routes.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New Route
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        @if($routes->count() > 0)
        <div class="card">
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Drag and drop rows to reorder routes by priority
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="sortable-routes">
                        <thead>
                            <tr>
                                <th width="50"></th>
                                <th>Route File</th>
                                <th>Service</th>
                                <th>Match</th>
                                <th>Rule</th>
                                <th>Type</th>
                                <th>Chain Class</th>
                                <th width="200">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="routes-tbody">
                            @foreach($routes as $route)
                            <tr class="draggable-item" data-id="{{ $route->id }}">
                                <td class="text-center">
                                    <i class="bi bi-grip-vertical text-muted" style="cursor: grab;"></i>
                                </td>
                                <td>
                                    @if($route->routeFile)
                                        <a href="{{ route('route-files.show', $route->routefile_id) }}" class="text-decoration-none">
                                            {{ $route->routeFile->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">No file</span>
                                    @endif
                                </td>
                                <td>
                                    @if($route->service)
                                        <a href="{{ route('services.show', $route->from_service_id) }}" class="text-decoration-none">
                                            {{ $route->service->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($route->match)
                                        <a href="{{ route('matches.show', $route->match_id) }}" class="text-decoration-none">
                                            {{ $route->match->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($route->rule)
                                        <a href="{{ route('rules.show', $route->rule_id) }}" class="text-decoration-none">
                                            {{ $route->rule->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($route->type)
                                        <span class="badge bg-info badge-type">{{ $route->type }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($route->chainclass)
                                        <code class="small">{{ Str::limit($route->chainclass, 15) }}</code>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('routes.show', $route->id) }}"
                                           class="btn btn-outline-primary"
                                           title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('routes.edit', $route->id) }}"
                                           class="btn btn-outline-warning"
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-outline-danger"
                                                onclick="confirmDelete({{ $route->id }})"
                                                title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $route->id }}"
                                          action="{{ route('routes.destroy', $route->id) }}"
                                          method="POST"
                                          class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-signpost-split-fill fs-1 text-muted d-block mb-3"></i>
                <h5 class="text-muted">No Routes Yet</h5>
                <p class="text-muted mb-4">Get started by creating your first route</p>
                <a href="{{ route('routes.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Create First Route
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this route?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}

// Initialize SortableJS
@if($routes->count() > 0)
const tbody = document.getElementById('routes-tbody');
const sortable = Sortable.create(tbody, {
    animation: 150,
    handle: '.bi-grip-vertical',
    ghostClass: 'sortable-ghost',
    onEnd: function(evt) {
        // Get new order
        const items = tbody.querySelectorAll('tr.draggable-item');
        const order = [];
        items.forEach((item, index) => {
            order.push({
                id: item.dataset.id,
                priority: index + 1
            });
        });

        // Send AJAX request to update order
        fetch('{{ route("routes.reorder") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ order: order })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message (optional)
                console.log('Routes reordered successfully');
            }
        })
        .catch(error => {
            console.error('Error reordering routes:', error);
            alert('Failed to save new order. Please refresh the page.');
        });
    }
});
@endif
</script>
@endpush
