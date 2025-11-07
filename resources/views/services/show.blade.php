@extends('layouts.app')

@section('title', $service->name . ' - LRMP')
@section('page-title', $service->name)
@section('page-description', 'Service Details')

@section('page-actions')
    <a href="{{ route('services.edit', $service->id) }}" class="btn btn-warning">
        <i class="bi bi-pencil"></i> Edit
    </a>
    <a href="{{ route('services.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0"><i class="bi bi-gear"></i> Service Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Name:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $service->name }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Type:</strong>
                    </div>
                    <div class="col-md-9">
                        @if($service->type)
                            @php
                                $badgeColors = [
                                    'REQ' => 'bg-primary',
                                    'NOT' => 'bg-warning',
                                    'SAME' => 'bg-info',
                                    'PUB' => 'bg-success',
                                    'END' => 'bg-danger'
                                ];
                                $color = $badgeColors[$service->type] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $color }} badge-type">{{ $service->type }}</span>
                        @else
                            <span class="text-muted">Not specified</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Description:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $service->description ?: 'No description provided' }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Created:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $service->created_at->format('M d, Y H:i') }}
                        <small class="text-muted">({{ $service->created_at->diffForHumans() }})</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <strong>Last Updated:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $service->updated_at->format('M d, Y H:i') }}
                        <small class="text-muted">({{ $service->updated_at->diffForHumans() }})</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="bi bi-signpost-split"></i> Routes Using This Service</h5>
                <span class="badge bg-primary">{{ $service->routes->count() }}</span>
            </div>
            <div class="card-body">
                @if($service->routes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Priority</th>
                                    <th>Route File</th>
                                    <th>Match</th>
                                    <th>Rule</th>
                                    <th>Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($service->routes as $route)
                                <tr>
                                    <td><span class="badge bg-secondary">{{ $route->priority }}</span></td>
                                    <td>{{ $route->routeFile->name ?? 'N/A' }}</td>
                                    <td>{{ $route->match->name ?? '-' }}</td>
                                    <td>{{ $route->rule->name ?? '-' }}</td>
                                    <td>
                                        @if($route->type)
                                            <span class="badge bg-info badge-type">{{ $route->type }}</span>
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
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-signpost-split fs-2 text-muted d-block mb-2"></i>
                        <p class="text-muted">No routes are using this service yet</p>
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
                <p class="card-text">Deleting this service cannot be undone. This may affect {{ $service->routes->count() }} route(s).</p>
                <button type="button" class="btn btn-danger w-100" onclick="confirmDelete()">
                    <i class="bi bi-trash"></i> Delete Service
                </button>
                <form id="delete-form"
                      action="{{ route('services.destroy', $service->id) }}"
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
    if (confirm('Are you sure you want to delete this service? This may affect {{ $service->routes->count() }} route(s).')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endpush
