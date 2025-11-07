@extends('layouts.app')

@section('title', $rule->name . ' - LRMP')
@section('page-title', $rule->name)
@section('page-description', 'Rule Details')

@section('page-actions')
    <a href="{{ route('rules.edit', $rule->id) }}" class="btn btn-warning">
        <i class="bi bi-pencil"></i> Edit
    </a>
    <a href="{{ route('rules.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0"><i class="bi bi-card-checklist"></i> Rule Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Name:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $rule->name }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Class:</strong>
                    </div>
                    <div class="col-md-9">
                        <code>{{ $rule->class }}</code>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Type:</strong>
                    </div>
                    <div class="col-md-9">
                        @if($rule->type)
                            @php
                                $badgeColors = [
                                    'REQ' => 'bg-primary',
                                    'NOT' => 'bg-warning',
                                    'SAME' => 'bg-info',
                                    'PUB' => 'bg-success',
                                    'END' => 'bg-danger'
                                ];
                                $color = $badgeColors[$rule->type] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $color }} badge-type">{{ $rule->type }}</span>
                        @else
                            <span class="text-muted">Not specified</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Delta:</strong>
                    </div>
                    <div class="col-md-9">
                        @if($rule->delta)
                            <a href="{{ route('deltas.show', $rule->delta_id) }}">
                                <i class="bi bi-arrow-left-right"></i> {{ $rule->delta->name }}
                            </a>
                        @else
                            <span class="text-muted">No delta assigned</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Description:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $rule->description ?: 'No description provided' }}
                    </div>
                </div>

                <hr>

                <h6 class="mb-3"><i class="bi bi-gear"></i> Configuration Details</h6>

                <div class="row mb-2">
                    <div class="col-md-3">
                        <strong>On Failure:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $rule->on_failure ?: '-' }}
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-3">
                        <strong>Matching Condition:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $rule->matching_cond ?: '-' }}
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-3">
                        <strong>Route Cond OK:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $rule->route_cond_ok ?: '-' }}
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-3">
                        <strong>Route Cond KO:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $rule->route_cond_ko ?: '-' }}
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-3">
                        <strong>Delta Next:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $rule->delta_next ?: '-' }}
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-3">
                        <strong>Delta Cond OK:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $rule->delta_cond_ok ?: '-' }}
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-3">
                        <strong>Delta Cond KO:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $rule->delta_cond_ko ?: '-' }}
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Created:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $rule->created_at->format('M d, Y H:i') }}
                        <small class="text-muted">({{ $rule->created_at->diffForHumans() }})</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <strong>Last Updated:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $rule->updated_at->format('M d, Y H:i') }}
                        <small class="text-muted">({{ $rule->updated_at->diffForHumans() }})</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="bi bi-signpost-split"></i> Routes Using This Rule</h5>
                <span class="badge bg-primary">{{ $rule->routes->count() }}</span>
            </div>
            <div class="card-body">
                @if($rule->routes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Priority</th>
                                    <th>Service</th>
                                    <th>Route File</th>
                                    <th>Match</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rule->routes as $route)
                                <tr>
                                    <td><span class="badge bg-secondary">{{ $route->priority }}</span></td>
                                    <td>{{ $route->service->name ?? '-' }}</td>
                                    <td>{{ $route->routeFile->name ?? 'N/A' }}</td>
                                    <td>{{ $route->match->name ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('routes.show', $route->id) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="View Route">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-signpost-split fs-2 text-muted d-block mb-2"></i>
                        <p class="text-muted">No routes are using this rule yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0"><i class="bi bi-exclamation-triangle"></i> Danger Zone</h5>
            </div>
            <div class="card-body">
                <p class="card-text">Deleting this rule cannot be undone. This may affect {{ $rule->routes->count() }} route(s).</p>
                <button type="button" class="btn btn-danger w-100" onclick="confirmDelete()">
                    <i class="bi bi-trash"></i> Delete Rule
                </button>
                <form id="delete-form"
                      action="{{ route('rules.destroy', $rule->id) }}"
                      method="POST"
                      class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete this rule? This may affect {{ $rule->routes->count() }} route(s).')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endpush
