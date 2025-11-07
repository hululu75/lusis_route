@extends('layouts.app')

@section('title', 'Route Files - LRMP')
@section('page-title', 'Route Files')
@section('page-description', 'Manage routing configuration files')

@section('page-actions')
    <a href="{{ route('route-files.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New Route File
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        @if($routeFiles->count() > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>File Name</th>
                                <th>Project</th>
                                <th>Routes</th>
                                <th>Created</th>
                                <th width="200">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($routeFiles as $routeFile)
                            <tr>
                                <td>
                                    <strong>{{ $routeFile->name }}</strong>
                                </td>
                                <td>
                                    <code class="text-primary">{{ $routeFile->file_name }}</code>
                                </td>
                                <td>
                                    @if($routeFile->project)
                                        <a href="{{ route('projects.show', $routeFile->project_id) }}" class="text-decoration-none">
                                            <i class="bi bi-folder"></i> {{ $routeFile->project->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">No project</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $routeFile->routes_count ?? 0 }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $routeFile->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
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
                                        <button type="button"
                                                class="btn btn-outline-danger"
                                                onclick="confirmDelete({{ $routeFile->id }})"
                                                title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $routeFile->id }}"
                                          action="{{ route('route-files.destroy', $routeFile->id) }}"
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
                <i class="bi bi-file-earmark-code-fill fs-1 text-muted d-block mb-3"></i>
                <h5 class="text-muted">No Route Files Yet</h5>
                <p class="text-muted mb-4">Get started by creating your first route file</p>
                <a href="{{ route('route-files.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Create First Route File
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
    if (confirm('Are you sure you want to delete this route file? This will also delete all associated routes.')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush
