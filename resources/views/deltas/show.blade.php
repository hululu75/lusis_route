@extends('layouts.app')

@section('title', $delta->name . ' - LRMP')
@section('page-title', $delta->name)
@section('page-description', 'Delta Transformation Details')

@section('page-actions')
    <a href="{{ route('deltas.edit', $delta->id) }}" class="btn btn-warning">
        <i class="bi bi-pencil"></i> Edit
    </a>
    <a href="{{ route('deltas.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0"><i class="bi bi-arrow-left-right"></i> Delta Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Name:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $delta->name }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Next:</strong>
                    </div>
                    <div class="col-md-9">
                        @if($delta->next)
                            <code class="text-info">{{ $delta->next }}</code>
                        @else
                            <span class="text-muted">Not specified</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Description:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $delta->description ?: 'No description provided' }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Created:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $delta->created_at->format('M d, Y H:i') }}
                        <small class="text-muted">({{ $delta->created_at->diffForHumans() }})</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <strong>Last Updated:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $delta->updated_at->format('M d, Y H:i') }}
                        <small class="text-muted">({{ $delta->updated_at->diffForHumans() }})</small>
                    </div>
                </div>
            </div>
        </div>

        @if($delta->definition)
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0"><i class="bi bi-code-square"></i> XML Definition</h5>
            </div>
            <div class="card-body">
                <pre class="bg-light p-3 rounded" style="max-height: 400px; overflow-y: auto;"><code>{{ $delta->definition }}</code></pre>
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="bi bi-card-checklist"></i> Rules Using This Delta</h5>
                <span class="badge bg-primary">{{ $delta->rules->count() }}</span>
            </div>
            <div class="card-body">
                @if($delta->rules->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Class</th>
                                    <th>Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($delta->rules as $rule)
                                <tr>
                                    <td><strong>{{ $rule->name }}</strong></td>
                                    <td><code class="small">{{ Str::limit($rule->class, 30) }}</code></td>
                                    <td>
                                        @if($rule->type)
                                            <span class="badge bg-info badge-type">{{ $rule->type }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('rules.show', $rule->id) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="View Rule">
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
                        <i class="bi bi-card-checklist fs-2 text-muted d-block mb-2"></i>
                        <p class="text-muted">No rules are using this delta yet</p>
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
                <p class="card-text">Deleting this delta cannot be undone. This may affect {{ $delta->rules->count() }} rule(s).</p>
                <button type="button" class="btn btn-danger w-100" onclick="confirmDelete()">
                    <i class="bi bi-trash"></i> Delete Delta
                </button>
                <form id="delete-form"
                      action="{{ route('deltas.destroy', $delta->id) }}"
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
    if (confirm('Are you sure you want to delete this delta? This may affect {{ $delta->rules->count() }} rule(s).')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endpush
