@extends('layouts.app')

@section('title', 'Matches - LRMP')
@section('page-title', 'Matches')
@section('page-description', 'Manage routing match conditions')

@section('page-actions')
    <a href="{{ route('matches.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New Match
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        @if($matches->count() > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Conditions</th>
                                <th>Description</th>
                                <th>Created</th>
                                <th width="200">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($matches as $match)
                            <tr>
                                <td>
                                    <strong>{{ $match->name }}</strong>
                                </td>
                                <td>
                                    @if($match->type)
                                        <span class="badge bg-info badge-type">{{ $match->type }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $match->conditions_count ?? 0 }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ Str::limit($match->description, 50) }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $match->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('matches.show', $match->id) }}"
                                           class="btn btn-outline-primary"
                                           title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('matches.edit', $match->id) }}"
                                           class="btn btn-outline-warning"
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-outline-danger"
                                                onclick="confirmDelete({{ $match->id }})"
                                                title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $match->id }}"
                                          action="{{ route('matches.destroy', $match->id) }}"
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
                <i class="bi bi-filter-circle-fill fs-1 text-muted d-block mb-3"></i>
                <h5 class="text-muted">No Matches Yet</h5>
                <p class="text-muted mb-4">Get started by creating your first match condition</p>
                <a href="{{ route('matches.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Create First Match
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
    if (confirm('Are you sure you want to delete this match? This may affect associated routes.')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush
