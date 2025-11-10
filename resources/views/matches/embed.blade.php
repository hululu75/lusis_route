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
        @if($matches->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Conditions</th>
                        <th>Description</th>
                        <th>Created</th>
                        <th width="150">Actions</th>
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
                                <span class="badge bg-info">{{ $match->type }}</span>
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
                                   title="View"
                                   target="_top">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('matches.edit', $match->id) }}"
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
            <i class="bi bi-filter-circle fs-1 text-muted d-block mb-3"></i>
            <h5 class="text-muted">No Matches Yet</h5>
            <p class="text-muted">Create a match using the modal form</p>
        </div>
        @endif
    </div>
</body>
</html>
