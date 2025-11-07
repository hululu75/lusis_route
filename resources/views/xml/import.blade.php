@extends('layouts.app')

@section('title', 'Import XML - LRMP')
@section('page-title', 'Import XML Configuration')
@section('page-description', 'Upload and import routing configuration from XML file')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-download"></i> Import XML Configuration</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    <strong>Important:</strong> The XML file should follow the Tango routing configuration format.
                    It should contain routes, matches, rules, and deltas definitions.
                </div>

                <form action="{{ route('xml.import.process') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="project_id" class="form-label">
                            Target Project <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('project_id') is-invalid @enderror"
                                id="project_id"
                                name="project_id"
                                required>
                            <option value="">-- Select a Project --</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            The imported routes will be added to this project
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="route_file_name" class="form-label">
                            Route File Name <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               class="form-control @error('route_file_name') is-invalid @enderror"
                               id="route_file_name"
                               name="route_file_name"
                               value="{{ old('route_file_name') }}"
                               placeholder="e.g., WEBTGIN Routes"
                               required>
                        @error('route_file_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            A descriptive name for this route file
                        </small>
                    </div>

                    <div class="mb-4">
                        <label for="xml_file" class="form-label">
                            XML File <span class="text-danger">*</span>
                        </label>
                        <input type="file"
                               class="form-control @error('xml_file') is-invalid @enderror"
                               id="xml_file"
                               name="xml_file"
                               accept=".xml"
                               required>
                        @error('xml_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Upload a routing_*.xml file (max 2MB)
                        </small>
                    </div>

                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6 class="card-title"><i class="bi bi-gear"></i> Import Behavior</h6>
                            <ul class="mb-0 small">
                                <li>Existing services, matches, rules, and deltas with the same name will be reused</li>
                                <li>New entities will be created automatically</li>
                                <li>Route priorities will be assigned sequentially</li>
                                <li>All changes are wrapped in a database transaction</li>
                            </ul>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('home') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload"></i> Import XML
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Example XML Structure -->
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-code-square"></i> Expected XML Structure</h6>
            </div>
            <div class="card-body">
                <pre class="bg-dark text-light p-3 rounded" style="font-size: 0.85rem;"><code>&lt;routing&gt;
  &lt;deltas&gt;
    &lt;delta name="D_ADD"&gt;...&lt;/delta&gt;
  &lt;/deltas&gt;

  &lt;matches&gt;
    &lt;match name="isHSM"&gt;
      &lt;condition field="tg:MTI" operator="SUP" value="5999"/&gt;
    &lt;/match&gt;
  &lt;/matches&gt;

  &lt;rules&gt;
    &lt;rule name="R_MON" class="MON" type="NOT" delta="D_ADD"/&gt;
  &lt;/rules&gt;

  &lt;routes&gt;
    &lt;route class="WEBTGINAPP"&gt;
      &lt;case cond="isHSM" rule="R_MON"/&gt;
    &lt;/route&gt;
  &lt;/routes&gt;
&lt;/routing&gt;</code></pre>
            </div>
        </div>
    </div>
</div>
@endsection
