@extends('layouts.app')

@section('title', 'Rules - LRMP')
@section('page-title', 'Rules')
@section('page-description', 'Manage routing rules')

@section('page-actions')
    <a href="{{ route('rules.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New Rule
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        @if($rules->count() > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Class</th>
                                <th>Type</th>
                                <th>Delta</th>
                                <th>Description</th>
                                <th>Created</th>
                                <th width="200">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rules as $rule)
                            <tr>
                                <td>
                                    <strong>{{ $rule->name }}</strong>
                                </td>
                                <td>
                                    <code class="small">{{ Str::limit($rule->class, 25) }}</code>
                                </td>
                                <td>
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
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($rule->delta)
                                        <a href="{{ route('deltas.show', $rule->delta_id) }}" class="text-decoration-none">
                                            {{ $rule->delta->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ Str::limit($rule->description, 40) }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $rule->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('rules.show', $rule->id) }}"
                                           class="btn btn-outline-primary"
                                           title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('rules.edit', $rule->id) }}"
                                           class="btn btn-outline-warning"
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-outline-danger"
                                                onclick="confirmDelete({{ $rule->id }})"
                                                title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $rule->id }}"
                                          action="{{ route('rules.destroy', $rule->id) }}"
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
                <i class="bi bi-card-checklist fs-1 text-muted d-block mb-3"></i>
                <h5 class="text-muted">No Rules Yet</h5>
                <p class="text-muted mb-4">Get started by creating your first routing rule</p>
                <a href="{{ route('rules.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Create First Rule
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
    if (confirm('Are you sure you want to delete this rule? This may affect associated routes.')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush
