@extends('layouts.app')

@section('title', $match->name . ' - LRMP')
@section('page-title', $match->name)
@section('page-description', 'Match Condition Details with Inline Editing')

@section('page-actions')
    <a href="{{ route('matches.edit', $match->id) }}" class="btn btn-warning">
        <i class="bi bi-pencil"></i> Edit Match
    </a>
    <a href="{{ route('matches.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0"><i class="bi bi-filter"></i> Match Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Name:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $match->name }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Type:</strong>
                    </div>
                    <div class="col-md-9">
                        @if($match->type)
                            <span class="badge bg-info badge-type">{{ $match->type }}</span>
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
                        {{ $match->description ?: 'No description' }}
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <strong>Created:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $match->created_at->format('M d, Y H:i') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Inline Conditions Editor -->
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-list-check"></i> Conditions
                    <span class="badge bg-primary ms-2" id="conditions-count">{{ $match->conditions->count() }}</span>
                </h5>
                <button class="btn btn-sm btn-success" onclick="addConditionRow()">
                    <i class="bi bi-plus-circle"></i> Add Condition
                </button>
            </div>
            <div class="card-body">
                <div id="alert-container"></div>

                <div class="table-responsive" id="conditions-table" @if($match->conditions->count() == 0) style="display: none;" @endif>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="30%">Field</th>
                                <th width="20%">Operator</th>
                                <th width="35%">Value</th>
                                <th width="15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="conditions-tbody">
                            @foreach($match->conditions as $condition)
                            <tr data-condition-id="{{ $condition->id }}">
                                <td>
                                    <input type="text"
                                           class="form-control form-control-sm condition-field"
                                           value="{{ $condition->field }}"
                                           data-original="{{ $condition->field }}">
                                </td>
                                <td>
                                    <select class="form-select form-select-sm condition-operator" data-original="{{ $condition->operator }}">
                                        <option value="EQUAL" {{ $condition->operator == 'EQUAL' ? 'selected' : '' }}>EQUAL</option>
                                        <option value="SUP" {{ $condition->operator == 'SUP' ? 'selected' : '' }}>SUP (>)</option>
                                        <option value="INF" {{ $condition->operator == 'INF' ? 'selected' : '' }}>INF (<)</option>
                                        <option value="ELT" {{ $condition->operator == 'ELT' ? 'selected' : '' }}>ELT (in list)</option>
                                        <option value="IN" {{ $condition->operator == 'IN' ? 'selected' : '' }}>IN (contains)</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text"
                                           class="form-control form-control-sm condition-value"
                                           value="{{ $condition->value }}"
                                           data-original="{{ $condition->value }}"
                                           placeholder="Optional">
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary btn-save-condition"
                                                onclick="saveCondition({{ $condition->id }})"
                                                style="display: none;"
                                                title="Save">
                                            <i class="bi bi-check"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary btn-cancel-condition"
                                                onclick="cancelEdit({{ $condition->id }})"
                                                style="display: none;"
                                                title="Cancel">
                                            <i class="bi bi-x"></i>
                                        </button>
                                        <button class="btn btn-outline-danger"
                                                onclick="deleteCondition({{ $condition->id }})"
                                                title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="text-muted text-center py-4" id="no-conditions-message" @if($match->conditions->count() > 0) style="display: none;" @endif>
                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                    No conditions defined yet. Click "Add Condition" to create one.
                </p>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Stats Card -->
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-graph-up"></i> Statistics</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Conditions:</span>
                    <strong><span id="stats-conditions">{{ $match->conditions->count() }}</span></strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Used in Routes:</span>
                    <strong>{{ $match->routes->count() }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Last Updated:</span>
                    <strong>{{ $match->updated_at->diffForHumans() }}</strong>
                </div>
            </div>
        </div>

        <!-- Routes Using This Match -->
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-signpost-split"></i> Routes Using This Match</h6>
            </div>
            <div class="card-body">
                @if($match->routes->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($match->routes as $route)
                    <a href="{{ route('routes.show', $route->id) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $route->service->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $route->routeFile->name }}</small>
                            </div>
                            @if($route->type)
                            <span class="badge bg-secondary">{{ $route->type }}</span>
                            @endif
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <p class="text-muted mb-0">Not used in any routes yet</p>
                @endif
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Danger Zone</h6>
            </div>
            <div class="card-body">
                <p class="small mb-2">Delete this match and all its conditions</p>
                <button class="btn btn-danger btn-sm" onclick="confirmDelete()">
                    <i class="bi bi-trash"></i> Delete Match
                </button>
                <form id="delete-form" action="{{ route('matches.destroy', $match->id) }}" method="POST" class="d-none">
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
// CSRF token for AJAX requests
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const matchId = {{ $match->id }};

// Show alert message
function showAlert(message, type = 'success') {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    document.getElementById('alert-container').innerHTML = alertHtml;

    // Auto-hide after 3 seconds
    setTimeout(() => {
        $('.alert').fadeOut();
    }, 3000);
}

// Detect changes in condition inputs
$(document).on('input change', '.condition-field, .condition-operator, .condition-value', function() {
    const row = $(this).closest('tr');
    const conditionId = row.data('condition-id');

    const field = row.find('.condition-field');
    const operator = row.find('.condition-operator');
    const value = row.find('.condition-value');

    const hasChanges =
        field.val() !== field.data('original') ||
        operator.val() !== operator.data('original') ||
        value.val() !== value.data('original');

    if (hasChanges) {
        row.find('.btn-save-condition, .btn-cancel-condition').show();
    } else {
        row.find('.btn-save-condition, .btn-cancel-condition').hide();
    }
});

// Add new condition row
function addConditionRow() {
    const tbody = document.getElementById('conditions-tbody');
    const table = document.getElementById('conditions-table');
    const noMessage = document.getElementById('no-conditions-message');

    if (noMessage) {
        noMessage.remove();
        table.style.display = '';
    }

    const newRow = document.createElement('tr');
    newRow.setAttribute('data-new', 'true');
    newRow.innerHTML = `
        <td>
            <input type="text" class="form-control form-control-sm new-condition-field" placeholder="e.g., tg:MTI" required>
        </td>
        <td>
            <select class="form-select form-select-sm new-condition-operator">
                <option value="EQUAL">EQUAL</option>
                <option value="SUP">SUP (>)</option>
                <option value="INF">INF (<)</option>
                <option value="ELT">ELT (in list)</option>
                <option value="IN">IN (contains)</option>
            </select>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm new-condition-value" placeholder="Optional">
        </td>
        <td>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-success" onclick="saveNewCondition(this)" title="Save">
                    <i class="bi bi-check"></i>
                </button>
                <button class="btn btn-secondary" onclick="cancelNewCondition(this)" title="Cancel">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        </td>
    `;

    tbody.appendChild(newRow);
    newRow.querySelector('.new-condition-field').focus();
}

// Save new condition
function saveNewCondition(button) {
    const row = button.closest('tr');
    const field = row.querySelector('.new-condition-field').value.trim();
    const operator = row.querySelector('.new-condition-operator').value;
    const value = row.querySelector('.new-condition-value').value.trim();

    if (!field) {
        alert('Field is required');
        return;
    }

    fetch('{{ route("match-conditions.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            match_id: matchId,
            field: field,
            operator: operator,
            value: value || null
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Reload to show the new condition
        } else {
            alert('Error: ' + (data.message || 'Failed to add condition'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to add condition');
    });
}

// Cancel new condition
function cancelNewCondition(button) {
    button.closest('tr').remove();

    // Check if we need to show the "no conditions" message
    const tbody = document.getElementById('conditions-tbody');
    if (tbody.children.length === 0) {
        const table = document.getElementById('conditions-table');
        table.style.display = 'none';
        const card = table.closest('.card-body');
        card.innerHTML += '<p class="text-muted text-center py-4" id="no-conditions-message"><i class="bi bi-inbox fs-3 d-block mb-2"></i>No conditions defined yet. Click "Add Condition" to create one.</p>';
    }
}

// Save existing condition
function saveCondition(conditionId) {
    const row = $(`tr[data-condition-id="${conditionId}"]`);
    const field = row.find('.condition-field').val().trim();
    const operator = row.find('.condition-operator').val();
    const value = row.find('.condition-value').val().trim();

    if (!field) {
        alert('Field is required');
        return;
    }

    fetch(`/match-conditions/${conditionId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            field: field,
            operator: operator,
            value: value || null
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            row.find('.condition-field').data('original', field);
            row.find('.condition-operator').data('original', operator);
            row.find('.condition-value').data('original', value);
            row.find('.btn-save-condition, .btn-cancel-condition').hide();
            showAlert('Condition updated successfully!');
        } else {
            alert('Error: ' + (data.message || 'Failed to update condition'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update condition');
    });
}

// Cancel edit
function cancelEdit(conditionId) {
    const row = $(`tr[data-condition-id="${conditionId}"]`);
    row.find('.condition-field').val(row.find('.condition-field').data('original'));
    row.find('.condition-operator').val(row.find('.condition-operator').data('original'));
    row.find('.condition-value').val(row.find('.condition-value').data('original'));
    row.find('.btn-save-condition, .btn-cancel-condition').hide();
}

// Delete condition
function deleteCondition(conditionId) {
    if (!confirm('Are you sure you want to delete this condition?')) {
        return;
    }

    fetch(`/match-conditions/${conditionId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const row = $(`tr[data-condition-id="${conditionId}"]`);
            row.fadeOut(300, function() {
                $(this).remove();
                updateConditionsCount();

                // Check if we need to show the "no conditions" message
                const tbody = document.getElementById('conditions-tbody');
                if (tbody.children.length === 0) {
                    const table = document.getElementById('conditions-table');
                    table.style.display = 'none';
                    const card = table.closest('.card-body');
                    card.innerHTML += '<p class="text-muted text-center py-4" id="no-conditions-message"><i class="bi bi-inbox fs-3 d-block mb-2"></i>No conditions defined yet. Click "Add Condition" to create one.</p>';
                }
            });
            showAlert('Condition deleted successfully!');
        } else {
            alert('Error: ' + (data.message || 'Failed to delete condition'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to delete condition');
    });
}

// Update conditions count
function updateConditionsCount() {
    const count = $('#conditions-tbody tr').length;
    $('#conditions-count').text(count);
    $('#stats-conditions').text(count);
}

// Confirm match deletion
function confirmDelete() {
    if (confirm('Are you sure you want to delete this match? This will also delete all associated conditions.')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endpush

@push('styles')
<style>
.condition-field:focus,
.condition-operator:focus,
.condition-value:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

#conditions-table tbody tr:hover {
    background-color: #f8f9fa;
}
</style>
@endpush
