@extends('layouts.app')

@section('title', 'Projects - LRMP')
@section('page-title', 'Projects')
@section('page-description', 'Manage your routing projects')

@section('page-actions')
    <a href="{{ route('projects.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New Project
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        @if($projects->count() > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Route Files</th>
                                <th>Created</th>
                                <th width="200">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($projects as $project)
                            <tr>
                                <td>
                                    <strong>{{ $project->name }}</strong>
                                </td>
                                <td>
                                    <small class="text-muted">{{ Str::limit($project->description, 60) }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $project->route_files_count }} files</span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $project->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('projects.show', $project->id) }}"
                                           class="btn btn-outline-primary"
                                           title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('projects.edit', $project->id) }}"
                                           class="btn btn-outline-warning"
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-outline-danger"
                                                onclick="confirmDelete({{ $project->id }})"
                                                title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $project->id }}"
                                          action="{{ route('projects.destroy', $project->id) }}"
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
                <i class="bi bi-folder-x fs-1 text-muted d-block mb-3"></i>
                <h5 class="text-muted">No Projects Yet</h5>
                <p class="text-muted mb-4">Get started by creating your first routing project</p>
                <a href="{{ route('projects.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Create First Project
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
    if (confirm('Are you sure you want to delete this project? This will also delete all associated route files and routes.')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush
