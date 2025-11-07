@extends('layouts.app')

@section('title', 'Deltas - LRMP')
@section('page-title', 'Deltas')
@section('page-description', 'Manage routing delta transformations')

@section('page-actions')
    <a href="{{ route('deltas.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New Delta
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        @if($deltas->count() > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Next</th>
                                <th>Rules</th>
                                <th>Description</th>
                                <th>Created</th>
                                <th width="200">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($deltas as $delta)
                            <tr>
                                <td>
                                    <strong>{{ $delta->name }}</strong>
                                </td>
                                <td>
                                    @if($delta->next)
                                        <code class="text-info">{{ Str::limit($delta->next, 30) }}</code>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $delta->rules_count ?? 0 }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ Str::limit($delta->description, 50) }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $delta->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('deltas.show', $delta->id) }}"
                                           class="btn btn-outline-primary"
                                           title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('deltas.edit', $delta->id) }}"
                                           class="btn btn-outline-warning"
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-outline-danger"
                                                onclick="confirmDelete({{ $delta->id }})"
                                                title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $delta->id }}"
                                          action="{{ route('deltas.destroy', $delta->id) }}"
                                          method="POST"
                                          class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-arrow-left-right fs-1 text-muted d-block mb-3"></i>
                <h5 class="text-muted">No Deltas Yet</h5>
                <p class="text-muted mb-4">Get started by creating your first delta transformation</p>
                <a href="{{ route('deltas.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Create First Delta
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this delta? This may affect associated rules.')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush
