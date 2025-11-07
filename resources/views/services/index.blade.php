@extends('layouts.app')

@section('title', 'Services - LRMP')
@section('page-title', 'Services')
@section('page-description', 'Manage routing services')

@section('page-actions')
    <a href="{{ route('services.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New Service
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        @if($services->count() > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Routes</th>
                                <th>Created</th>
                                <th width="200">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($services as $service)
                            <tr>
                                <td>
                                    <strong>{{ $service->name }}</strong>
                                </td>
                                <td>
                                    @if($service->type)
                                        @php
                                            $badgeColors = [
                                                'REQ' => 'bg-primary',
                                                'NOT' => 'bg-warning',
                                                'SAME' => 'bg-info',
                                                'PUB' => 'bg-success',
                                                'END' => 'bg-danger'
                                            ];
                                            $color = $badgeColors[$service->type] ?? 'bg-secondary';
                                        @endphp
                                        <span class="badge {{ $color }} badge-type">{{ $service->type }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ Str::limit($service->description, 60) }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $service->routes_count ?? 0 }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $service->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('services.show', $service->id) }}"
                                           class="btn btn-outline-primary"
                                           title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('services.edit', $service->id) }}"
                                           class="btn btn-outline-warning"
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-outline-danger"
                                                onclick="confirmDelete({{ $service->id }})"
                                                title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $service->id }}"
                                          action="{{ route('services.destroy', $service->id) }}"
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
                <i class="bi bi-gear-fill fs-1 text-muted d-block mb-3"></i>
                <h5 class="text-muted">No Services Yet</h5>
                <p class="text-muted mb-4">Get started by creating your first service</p>
                <a href="{{ route('services.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Create First Service
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
    if (confirm('Are you sure you want to delete this service? This may affect associated routes.')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush
