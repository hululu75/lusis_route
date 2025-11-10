@extends('layouts.app')

@section('title', 'Create Project - LRMP')
@section('page-title', 'Create New Project')
@section('page-description', 'Add a new routing project to your system')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('projects.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">
                            Project Name <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               placeholder="e.g., Production Routing Config"
                               required
                               autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            A unique name to identify this project
                        </small>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description"
                                  name="description"
                                  rows="4"
                                  placeholder="Describe the purpose of this project...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($projects->count() > 0)
                    <div class="mb-4">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="bi bi-files"></i> Copy From Existing Project</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="copy_from_project"
                                           name="copy_from_project"
                                           value="1"
                                           {{ old('copy_from_project') ? 'checked' : '' }}
                                           onchange="toggleCopySelect()">
                                    <label class="form-check-label" for="copy_from_project">
                                        Copy all data from an existing project
                                    </label>
                                </div>

                                <div id="copy-select-wrapper" style="display: none;">
                                    <label for="copy_project_id" class="form-label">Select Project to Copy From</label>
                                    <select class="form-select @error('copy_project_id') is-invalid @enderror"
                                            id="copy_project_id"
                                            name="copy_project_id">
                                        <option value="">-- Select a project --</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}" {{ old('copy_project_id') == $project->id ? 'selected' : '' }}>
                                                {{ $project->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('copy_project_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        This will copy all services, matches, deltas, rules, route files, and routes
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('projects.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Create Project
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleCopySelect() {
    const checkbox = document.getElementById('copy_from_project');
    const wrapper = document.getElementById('copy-select-wrapper');
    const select = document.getElementById('copy_project_id');

    if (checkbox.checked) {
        wrapper.style.display = 'block';
        select.required = true;
    } else {
        wrapper.style.display = 'none';
        select.required = false;
        select.value = '';
    }
}

// Initialize on page load in case of old() values
document.addEventListener('DOMContentLoaded', function() {
    const checkbox = document.getElementById('copy_from_project');
    if (checkbox && checkbox.checked) {
        toggleCopySelect();
    }
});
</script>
@endpush
