@extends('layouts.app')

@section('title', 'Create Rule - LRMP')
@section('page-title', 'Create Rule')
@section('page-description', 'Add a new routing rule')

@section('page-actions')
    <a href="{{ route('rules.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Rules
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-10 col-xl-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('rules.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
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

                        <div class="col-md-6 mb-3">
                            <label for="class" class="form-label">Class <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('class') is-invalid @enderror"
                                   id="class"
                                   name="class"
                                   value="{{ old('class') }}"
                                   required>
                            @error('class')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
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

                        <div class="col-md-6 mb-3">
                            <label for="delta_id" class="form-label">Delta</label>
                            <select class="form-select @error('delta_id') is-invalid @enderror"
                                    id="delta_id"
                                    name="delta_id">
                                <option value="">-- Select Delta --</option>
                                @foreach($deltas as $delta)
                                    <option value="{{ $delta->id }}" {{ old('delta_id') == $delta->id ? 'selected' : '' }}>
                                        {{ $delta->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('delta_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">
                    <h5 class="mb-3">Failure & Condition Settings</h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="on_failure" class="form-label">On Failure</label>
                            <input type="text"
                                   class="form-control @error('on_failure') is-invalid @enderror"
                                   id="on_failure"
                                   name="on_failure"
                                   value="{{ old('on_failure') }}">
                            @error('on_failure')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="matching_cond" class="form-label">Matching Condition</label>
                            <input type="text"
                                   class="form-control @error('matching_cond') is-invalid @enderror"
                                   id="matching_cond"
                                   name="matching_cond"
                                   value="{{ old('matching_cond') }}">
                            @error('matching_cond')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">
                    <h5 class="mb-3">Route Conditions</h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="route_cond_ok" class="form-label">Route Condition OK</label>
                            <input type="text"
                                   class="form-control @error('route_cond_ok') is-invalid @enderror"
                                   id="route_cond_ok"
                                   name="route_cond_ok"
                                   value="{{ old('route_cond_ok') }}">
                            @error('route_cond_ok')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="route_cond_ko" class="form-label">Route Condition KO</label>
                            <input type="text"
                                   class="form-control @error('route_cond_ko') is-invalid @enderror"
                                   id="route_cond_ko"
                                   name="route_cond_ko"
                                   value="{{ old('route_cond_ko') }}">
                            @error('route_cond_ko')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">
                    <h5 class="mb-3">Delta Conditions & Next Steps</h5>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="delta_next" class="form-label">Delta Next</label>
                            <input type="text"
                                   class="form-control @error('delta_next') is-invalid @enderror"
                                   id="delta_next"
                                   name="delta_next"
                                   value="{{ old('delta_next') }}">
                            @error('delta_next')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="delta_cond_ok" class="form-label">Delta Condition OK</label>
                            <input type="text"
                                   class="form-control @error('delta_cond_ok') is-invalid @enderror"
                                   id="delta_cond_ok"
                                   name="delta_cond_ok"
                                   value="{{ old('delta_cond_ok') }}">
                            @error('delta_cond_ok')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="delta_cond_ko" class="form-label">Delta Condition KO</label>
                            <input type="text"
                                   class="form-control @error('delta_cond_ko') is-invalid @enderror"
                                   id="delta_cond_ko"
                                   name="delta_cond_ko"
                                   value="{{ old('delta_cond_ko') }}">
                            @error('delta_cond_ko')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description"
                                  name="description"
                                  rows="4">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Optional description for this rule</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('rules.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Create Rule
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
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const nameInput = document.getElementById('name');

    // Prefix mapping based on type
    const prefixMap = {
        'REQ': 'R_',
        'NOT': 'N_',
        'SAME': 'S_',
        'PUB': 'P_',
        'END': 'E_'
    };

    // Function to update name with prefix
    function updateNamePrefix() {
        const selectedType = typeSelect.value;
        const currentName = nameInput.value;

        if (!selectedType) return;

        const newPrefix = prefixMap[selectedType];
        if (!newPrefix) return;

        // Remove old prefix if exists
        let nameWithoutPrefix = currentName;
        Object.values(prefixMap).forEach(prefix => {
            if (currentName.startsWith(prefix)) {
                nameWithoutPrefix = currentName.substring(prefix.length);
            }
        });

        // Add new prefix
        nameInput.value = newPrefix + nameWithoutPrefix;
    }

    // Listen to type changes
    typeSelect.addEventListener('change', updateNamePrefix);

    // If name is empty when type changes, just add the prefix
    typeSelect.addEventListener('change', function() {
        if (nameInput.value.trim() === '') {
            const selectedType = typeSelect.value;
            if (selectedType && prefixMap[selectedType]) {
                nameInput.value = prefixMap[selectedType];
            }
        }
    });
});
</script>
@endpush
