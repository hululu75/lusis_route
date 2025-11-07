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
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Priority</th>
                                    <th>Service</th>
                                    <th>Match</th>
                                    <th>Rule</th>
                                    <th>Type</th>
                                    <th>Chain Class</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($routeFile->routes->sortBy('priority') as $route)
                                <tr>
                                    <td><span class="badge bg-secondary">{{ $route->priority }}</span></td>
                                    <td>{{ $route->service->name ?? '-' }}</td>
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

@push('scripts')
<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete this route file? This will also delete {{ $routeFile->routes->count() }} route(s).')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endpush
