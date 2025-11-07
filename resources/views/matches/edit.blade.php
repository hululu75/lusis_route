@extends('layouts.app')

@section('title', 'Edit Match - LRMP')
@section('page-title', 'Edit Match')
@section('page-description', 'Update match condition details')

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
                <form action="{{ route('matches.update', $match->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               name="name"
                               value="{{ old('name', $match->name) }}"
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
                            <option value="AND" {{ old('type', $match->type) == 'AND' ? 'selected' : '' }}>AND</option>
                            <option value="OR" {{ old('type', $match->type) == 'OR' ? 'selected' : '' }}>OR</option>
                            <option value="NOT" {{ old('type', $match->type) == 'NOT' ? 'selected' : '' }}>NOT</option>
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
                                  rows="4">{{ old('description', $match->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Optional description for this match condition</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('matches.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update Match
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
