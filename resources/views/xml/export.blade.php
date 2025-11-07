@extends('layouts.app')

@section('title', 'Export XML - LRMP')
@section('page-title', 'Export XML Configuration')
@section('page-description', 'Export routing configuration to XML file')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-upload"></i> Export XML Configuration</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    <strong>Tip:</strong> Select a route file to export. The XML will include all routes, matches, rules, and deltas associated with that file.
                </div>

                <form action="{{ route('xml.export.process') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="filter_project" class="form-label">
                            Filter by Project (Optional)
                        </label>
                        <select class="form-select" id="filter_project" onchange="filterRouteFiles()">
                            <option value="">-- All Projects --</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">
                                    {{ $project->name }} ({{ $project->routeFiles->count() }} files)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="route_file_id" class="form-label">
                            Route File to Export <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('route_file_id') is-invalid @enderror"
                                id="route_file_id"
                                name="route_file_id"
                                required>
                            <option value="">-- Select a Route File --</option>
                            @foreach($routeFiles as $routeFile)
                                <option value="{{ $routeFile->id }}"
                                        data-project-id="{{ $routeFile->project_id }}"
                                        {{ old('route_file_id') == $routeFile->id ? 'selected' : '' }}>
                                    {{ $routeFile->project->name }} - {{ $routeFile->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('route_file_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Choose the route file you want to export
                        </small>
                    </div>

                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6 class="card-title"><i class="bi bi-gear"></i> Export Details</h6>
                            <ul class="mb-0 small">
                                <li>The XML will include all routes in the selected route file</li>
                                <li>Associated matches, rules, and deltas will be included</li>
                                <li>The file will be formatted with proper indentation</li>
                                <li>Compatible with Tango routing configuration format</li>
                            </ul>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('home') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-download"></i> Export to XML
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Quick Export Links -->
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-lightning"></i> Quick Export</h6>
            </div>
            <div class="card-body">
                @if($routeFiles->count() > 0)
                <div class="list-group">
                    @foreach($routeFiles->take(5) as $routeFile)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $routeFile->name }}</strong>
                            <br>
                            <small class="text-muted">
                                {{ $routeFile->project->name }} - {{ $routeFile->routes->count() }} routes
                            </small>
                        </div>
                        <form action="{{ route('xml.export.process') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="route_file_id" value="{{ $routeFile->id }}">
                            <button type="submit" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-download"></i> Export
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @if($routeFiles->count() > 5)
                <p class="text-center text-muted mt-3 mb-0">
                    <small>Showing 5 of {{ $routeFiles->count() }} route files</small>
                </p>
                @endif
                @else
                <p class="text-muted text-center mb-0">
                    No route files available for export. <a href="{{ route('route-files.create') }}">Create one</a>
                </p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterRouteFiles() {
    const projectId = document.getElementById('filter_project').value;
    const routeFileSelect = document.getElementById('route_file_id');
    const options = routeFileSelect.getElementsByTagName('option');

    for (let i = 1; i < options.length; i++) {
        const option = options[i];
        const optionProjectId = option.getAttribute('data-project-id');

        if (projectId === '' || projectId === optionProjectId) {
            option.style.display = '';
        } else {
            option.style.display = 'none';
        }
    }

    // Reset selection if current selection is hidden
    if (routeFileSelect.selectedIndex > 0 && options[routeFileSelect.selectedIndex].style.display === 'none') {
        routeFileSelect.selectedIndex = 0;
    }
}
</script>
@endpush
