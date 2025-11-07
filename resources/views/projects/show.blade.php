@extends('layouts.app')

@section('title', $project->name . ' - LRMP')
@section('page-title', $project->name)
@section('page-description', $project->description)

@section('page-actions')
    <div class="btn-group">
        <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <button type="button" class="btn btn-danger" onclick="confirmDelete()">
            <i class="bi bi-trash"></i> Delete
        </button>
    </div>
    <form id="delete-form" action="{{ route('projects.destroy', $project->id) }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('content')
<div class="row g-4">
    <!-- Project Info Card -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Project Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td>{{ $project->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Route Files:</strong></td>
                        <td><span class="badge bg-primary">{{ $project->routeFiles->count() }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Total Routes:</strong></td>
                        <td><span class="badge bg-info">{{ $project->routeFiles->sum(function($rf) { return $rf->routes->count(); }) }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Created:</strong></td>
                        <td>{{ $project->created_at->format('M d, Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Updated:</strong></td>
                        <td>{{ $project->updated_at->diffForHumans() }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-lightning"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('route-files.create', ['project_id' => $project->id]) }}" class="btn btn-outline-primary">
                        <i class="bi bi-plus-circle"></i> Add Route File
                    </a>
                    <a href="#" class="btn btn-outline-secondary">
                        <i class="bi bi-download"></i> Export to XML
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Route Files List -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-file-earmark-code"></i> Route Files</h5>
                <a href="{{ route('route-files.create', ['project_id' => $project->id]) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle"></i> New File
                </a>
            </div>
            <div class="card-body">
                @if($project->routeFiles->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($project->routeFiles as $routeFile)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <i class="bi bi-file-code text-primary"></i>
                                    <a href="{{ route('route-files.show', $routeFile->id) }}" class="text-decoration-none">
                                        {{ $routeFile->name }}
                                    </a>
                                </h6>
                                <small class="text-muted d-block mb-2">
                                    {{ $routeFile->description }}
                                </small>
                                <div>
                                    <span class="badge bg-light text-dark">
                                        <i class="bi bi-file-text"></i> {{ $routeFile->file_name }}
                                    </span>
                                    <span class="badge bg-info">
                                        {{ $routeFile->routes->count() }} routes
                                    </span>
                                </div>
                            </div>
                            <div class="btn-group btn-group-sm ms-3">
                                <a href="{{ route('route-files.show', $routeFile->id) }}"
                                   class="btn btn-outline-primary"
                                   title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('route-files.edit', $routeFile->id) }}"
                                   class="btn btn-outline-warning"
                                   title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </div>

                        @if($routeFile->routes->count() > 0)
                        <div class="mt-3">
                            <small class="text-muted fw-bold">Routes in this file:</small>
                            <div class="mt-2">
                                @foreach($routeFile->routes->take(3) as $route)
                                <span class="badge bg-secondary me-1 mb-1">
                                    {{ $route->service->name ?? 'N/A' }}
                                    @if($route->type)
                                        <span class="badge bg-light text-dark ms-1">{{ $route->type }}</span>
                                    @endif
                                </span>
                                @endforeach
                                @if($routeFile->routes->count() > 3)
                                <span class="badge bg-light text-dark">+{{ $routeFile->routes->count() - 3 }} more</span>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-inbox fs-3 text-muted d-block mb-2"></i>
                    <p class="text-muted mb-3">No route files in this project yet</p>
                    <a href="{{ route('route-files.create', ['project_id' => $project->id]) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Create First Route File
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete this project? This will permanently delete all route files and routes associated with it.')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endpush
