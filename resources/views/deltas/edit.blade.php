@extends('layouts.app')

@section('title', 'Edit Delta - LRMP')
@section('page-title', 'Edit Delta')
@section('page-description', 'Update delta transformation details')

@section('page-actions')
    <a href="{{ route('deltas.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Deltas
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-10 col-xl-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('deltas.update', $delta->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               name="name"
                               value="{{ old('name', $delta->name) }}"
                               required
                               autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="next" class="form-label">Next</label>
                        <input type="text"
                               class="form-control @error('next') is-invalid @enderror"
                               id="next"
                               name="next"
                               value="{{ old('next', $delta->next) }}">
                        @error('next')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Next delta or action reference</div>
                    </div>

                    <div class="mb-3">
                        <label for="definition" class="form-label">Definition (XML)</label>
                        <textarea class="form-control @error('definition') is-invalid @enderror"
                                  id="definition"
                                  name="definition"
                                  rows="10"
                                  style="font-family: 'Courier New', monospace; font-size: 0.9rem;">{{ old('definition', $delta->definition) }}</textarea>
                        @error('definition')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">XML definition for delta transformation</div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description"
                                  name="description"
                                  rows="4">{{ old('description', $delta->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Optional description for this delta</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('deltas.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update Delta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
