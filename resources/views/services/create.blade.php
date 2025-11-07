@extends('layouts.app')

@section('title', 'Create Service - LRMP')
@section('page-title', 'Create Service')
@section('page-description', 'Add a new routing service')

@section('page-actions')
    <a href="{{ route('services.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Services
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8 col-xl-6">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('services.store') }}" method="POST">
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
                            <option value="">-- Select Type --</option>
                            <option value="REQ" {{ old('type') == 'REQ' ? 'selected' : '' }}>REQ - Request</option>
                            <option value="NOT" {{ old('type') == 'NOT' ? 'selected' : '' }}>NOT - Notification</option>
                            <option value="SAME" {{ old('type') == 'SAME' ? 'selected' : '' }}>SAME - Same</option>
                            <option value="PUB" {{ old('type') == 'PUB' ? 'selected' : '' }}>PUB - Public</option>
                            <option value="END" {{ old('type') == 'END' ? 'selected' : '' }}>END - Endpoint</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
                        <div class="form-text">Optional description for this service</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('services.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Create Service
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
