@extends('layouts.app')

@section('title', 'Create Match - LRMP')
@section('page-title', 'Create Match')
@section('page-description', 'Add a new routing match condition')

@section('page-actions')
    <a href="{{ route('matches.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Matches
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8 col-xl-6">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('matches.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               required
                               autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select class="form-select @error('type') is-invalid @enderror"
                                id="type"
                                name="type">
                            <option value="">-- Select Type (Optional) --</option>
                            <option value="AND" {{ old('type') == 'AND' ? 'selected' : '' }}>AND</option>
                            <option value="OR" {{ old('type') == 'OR' ? 'selected' : '' }}>OR</option>
                            <option value="NOT" {{ old('type') == 'NOT' ? 'selected' : '' }}>NOT</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">How multiple conditions should be evaluated</div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description"
                                  name="description"
                                  rows="4">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Optional description for this match condition</div>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> After creating the match, you can add specific conditions (field, operator, value) on the details page.
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('matches.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Create Match
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
