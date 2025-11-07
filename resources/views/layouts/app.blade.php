<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Lusis Route Management Platform')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- SortableJS for drag-and-drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <!-- Vis.js for flow diagrams -->
    <script src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>
    <link href="https://unpkg.com/vis-network/styles/vis-network.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .sidebar .nav-link {
            color: #ecf0f1;
            padding: 0.75rem 1.5rem;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            border-left-color: #3498db;
        }
        .sidebar .nav-link.active {
            background-color: rgba(52, 152, 219, 0.2);
            border-left-color: #3498db;
            font-weight: 600;
        }
        .sidebar .nav-link i {
            margin-right: 0.5rem;
            width: 20px;
        }
        .content-wrapper {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .page-header {
            background: white;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid #e9ecef;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }
        .badge-type {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
            font-weight: 600;
        }
        .draggable-item {
            cursor: move;
            transition: background-color 0.2s;
        }
        .draggable-item:hover {
            background-color: #f8f9fa;
        }
        .sortable-ghost {
            opacity: 0.4;
            background: #e3f2fd;
        }
        .brand-logo {
            padding: 1.5rem;
            text-align: center;
            color: #ecf0f1;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 1rem;
        }
        .brand-logo h4 {
            margin: 0;
            font-weight: 700;
            font-size: 1.25rem;
        }
        .brand-logo small {
            font-size: 0.75rem;
            opacity: 0.8;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        .alert-dismissible {
            animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse" id="sidebarMenu">
                <div class="position-sticky">
                    <div class="brand-logo">
                        <h4><i class="bi bi-diagram-3"></i> LRMP</h4>
                        <small>Lusis Route Manager</small>
                    </div>

                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ route('home') }}">
                                <i class="bi bi-house-door"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('projects*') ? 'active' : '' }}" href="{{ route('projects.index') }}">
                                <i class="bi bi-folder"></i> Projects
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('services*') ? 'active' : '' }}" href="{{ route('services.index') }}">
                                <i class="bi bi-gear"></i> Services
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('route-files*') ? 'active' : '' }}" href="{{ route('route-files.index') }}">
                                <i class="bi bi-file-earmark-code"></i> Route Files
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('matches*') ? 'active' : '' }}" href="{{ route('matches.index') }}">
                                <i class="bi bi-filter"></i> Matches
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('deltas*') ? 'active' : '' }}" href="{{ route('deltas.index') }}">
                                <i class="bi bi-arrow-left-right"></i> Deltas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('rules*') ? 'active' : '' }}" href="{{ route('rules.index') }}">
                                <i class="bi bi-card-checklist"></i> Rules
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('routes*') ? 'active' : '' }}" href="{{ route('routes.index') }}">
                                <i class="bi bi-signpost-split"></i> Routes
                            </a>
                        </li>
                    </ul>

                    <hr style="border-color: rgba(255,255,255,0.2); margin: 1.5rem 1rem;">

                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('xml/import') ? 'active' : '' }}" href="{{ route('xml.import') }}">
                                <i class="bi bi-download"></i> Import XML
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('xml/export') ? 'active' : '' }}" href="{{ route('xml.export') }}">
                                <i class="bi bi-upload"></i> Export XML
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-question-circle"></i> Documentation
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content-wrapper">
                <!-- Mobile Menu Toggle -->
                <div class="d-md-none py-3">
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
                        <i class="bi bi-list"></i> Menu
                    </button>
                </div>

                <!-- Page Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-0">@yield('page-title', 'Dashboard')</h2>
                            <p class="text-muted mb-0">@yield('page-description', '')</p>
                        </div>
                        <div>
                            @yield('page-actions')
                        </div>
                    </div>
                </div>

                <!-- Flash Messages -->
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <!-- Content -->
                <div class="content-area px-3 pb-4">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (for convenience) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>

    @stack('scripts')
</body>
</html>
