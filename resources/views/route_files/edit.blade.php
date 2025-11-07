@extends('layouts.app')

@section('title', 'Edit Route File - LRMP')
@section('page-title', 'Edit Route File')
@section('page-description', 'Update route file details')

@section('page-actions')
    <a href="{{ route('route-files.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Route Files
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8 col-xl-6">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('route-files.update', $routeFile->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="project_id" class="form-label">Project <span class="text-danger">*</span></label>
                        <select class="form-select @error('project_id') is-invalid @enderror"
                                id="project_id"
                                name="project_id"
                                required>
                            <option value="">-- Select Project --</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id', $routeFile->project_id) == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               name="name"
                               value="{{ old('name', $routeFile->name) }}"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="file_name" class="form-label">File Name <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('file_name') is-invalid @enderror"
                               id="file_name"
                               name="file_name"
                               value="{{ old('file_name', $routeFile->file_name) }}"
                               placeholder="e.g., routing.xml"
                               required>
                        @error('file_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">The actual file name on the system</div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description"
                                  name="description"
                                  rows="4">{{ old('description', $routeFile->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Optional description for this route file</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('route-files.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update Route File
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
