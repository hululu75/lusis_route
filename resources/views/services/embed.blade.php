<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <div class="container-fluid p-3">
        @if($services->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Routes</th>
                        <th>Created</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($services as $service)
                    <tr>
                        <td>
                            <strong>{{ $service->name }}</strong>
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
                                   title="View"
                                   target="_top">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('services.edit', $service->id) }}"
                                   class="btn btn-outline-warning"
                                   title="Edit"
                                   target="_top">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-gear-fill fs-1 text-muted d-block mb-3"></i>
            <h5 class="text-muted">No Services Yet</h5>
            <p class="text-muted">Create a service using the modal form</p>
        </div>
        @endif
    </div>
</body>
</html>
