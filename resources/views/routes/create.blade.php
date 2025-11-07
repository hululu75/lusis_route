@extends('layouts.app')

@section('title', 'Create Route - LRMP')
@section('page-title', 'Create Route')
@section('page-description', 'Add a new routing configuration')

@section('page-actions')
    <a href="{{ route('routes.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Routes
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-10 col-xl-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('routes.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="routefile_id" class="form-label">Route File <span class="text-danger">*</span></label>
                            <select class="form-select @error('routefile_id') is-invalid @enderror"
                                    id="routefile_id"
                                    name="routefile_id"
                                    required>
                                <option value="">-- Select Route File --</option>
                                @foreach($routeFiles as $routeFile)
                                    <option value="{{ $routeFile->id }}" {{ old('routefile_id') == $routeFile->id ? 'selected' : '' }}>
                                        {{ $routeFile->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('routefile_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="service_id" class="form-label">Service <span class="text-danger">*</span></label>
                            <select class="form-select @error('service_id') is-invalid @enderror"
                                    id="service_id"
                                    name="service_id"
                                    required>
                                <option value="">-- Select Service --</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                        {{ $service->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('service_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="match_id" class="form-label">Match</label>
                            <select class="form-select @error('match_id') is-invalid @enderror"
                                    id="match_id"
                                    name="match_id">
                                <option value="">-- Select Match (Optional) --</option>
                                @foreach($matches as $match)
                                    <option value="{{ $match->id }}" {{ old('match_id') == $match->id ? 'selected' : '' }}>
                                        {{ $match->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('match_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="rule_id" class="form-label">Rule</label>
                            <select class="form-select @error('rule_id') is-invalid @enderror"
                                    id="rule_id"
                                    name="rule_id">
                                <option value="">-- Select Rule (Optional) --</option>
                                @foreach($rules as $rule)
                                    <option value="{{ $rule->id }}" {{ old('rule_id') == $rule->id ? 'selected' : '' }}>
                                        {{ $rule->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('rule_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="chainclass" class="form-label">Chain Class</label>
                            <input type="text"
                                   class="form-control @error('chainclass') is-invalid @enderror"
                                   id="chainclass"
                                   name="chainclass"
                                   value="{{ old('chainclass') }}">
                            @error('chainclass')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Optional chain class identifier</div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-select @error('type') is-invalid @enderror"
                                    id="type"
                                    name="type">
                                <option value="">-- Select Type --</option>
                                <option value="REQ" {{ old('type') == 'REQ' ? 'selected' : '' }}>REQ</option>
                                <option value="NOT" {{ old('type') == 'NOT' ? 'selected' : '' }}>NOT</option>
                                <option value="SAME" {{ old('type') == 'SAME' ? 'selected' : '' }}>SAME</option>
                                <option value="PUB" {{ old('type') == 'PUB' ? 'selected' : '' }}>PUB</option>
                                <option value="END" {{ old('type') == 'END' ? 'selected' : '' }}>END</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                            <input type="number"
                                   class="form-control @error('priority') is-invalid @enderror"
                                   id="priority"
                                   name="priority"
                                   value="{{ old('priority', 1) }}"
                                   min="1"
                                   required>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('routes.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Create Route
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
