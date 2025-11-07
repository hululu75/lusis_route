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
                        <label for="type" class="form-label">Message Type</label>
                        <select class="form-select @error('type') is-invalid @enderror"
                                id="type"
                                name="type">
                            <option value="">-- Select Message Type (Optional) --</option>
                            <option value="REQ" {{ old('type', $match->type) == 'REQ' ? 'selected' : '' }}>REQ - Request</option>
                            <option value="NOT" {{ old('type', $match->type) == 'NOT' ? 'selected' : '' }}>NOT - Notification</option>
                            <option value="SAME" {{ old('type', $match->type) == 'SAME' ? 'selected' : '' }}>SAME - Same</option>
                            <option value="PUB" {{ old('type', $match->type) == 'PUB' ? 'selected' : '' }}>PUB - Public</option>
                            <option value="END" {{ old('type', $match->type) == 'END' ? 'selected' : '' }}>END - Endpoint</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Message type for this match condition</div>
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
