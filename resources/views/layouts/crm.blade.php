<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'CRM Legislativo')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #1e3a8a 0%, #1e40af 100%);
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }
        .main-content {
            background-color: #f8fafc;
            min-height: 100vh;
        }
        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }
        .metric-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .metric-card.success {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .metric-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .metric-card.info {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .navbar-brand {
            font-weight: 600;
            color: #1e40af !important;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-12 col-md-3 col-lg-2 sidebar">
                <div class="p-3">
                    <h4 class="text-white mb-4">
                        <i class="bi bi-building"></i>
                        CRM Legislativo
                    </h4>
                    
                    <nav class="nav flex-column">
                        <a class="nav-link {{ Request::routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i>
                            Dashboard
                        </a>
                        
                        <a class="nav-link {{ Request::routeIs('cidadaos.*') ? 'active' : '' }}" href="{{ route('cidadaos.index') }}">
                            <i class="bi bi-people me-2"></i>
                            Cidadãos
                        </a>
                        
                        <a class="nav-link {{ Request::routeIs('demandas.*') ? 'active' : '' }}" href="{{ route('demandas.index') }}">
                            <i class="bi bi-clipboard-check me-2"></i>
                            Demandas
                        </a>

                        <a class="nav-link {{ Request::routeIs('agendamentos.*') ? 'active' : '' }}" href="{{ route('agendamentos.index') }}">
                            <i class="bi bi-calendar-event me-2"></i>
                            Follow-up
                        </a>

                        <a class="nav-link {{ Request::routeIs('templates.*') ? 'active' : '' }}" href="{{ route('templates.index') }}">
                            <i class="bi bi-file-text me-2"></i>
                            Templates
                        </a>

                        <a class="nav-link" href="#">
                            <i class="bi bi-clock-history me-2"></i>
                            Interações
                        </a>

                        <a class="nav-link" href="#">
                            <i class="bi bi-tags me-2"></i>
                            Segmentação
                        </a>
                        
                        <a class="nav-link {{ Request::routeIs('relatorios.*') ? 'active' : '' }}" href="#">
                            <i class="bi bi-graph-up me-2"></i>
                            Relatórios
                        </a>
                        
                        <hr class="text-white-50 my-3">
                        
                        <a class="nav-link" href="#">
                            <i class="bi bi-gear me-2"></i>
                            Configurações
                        </a>
                        
                        <a class="nav-link" href="#">
                            <i class="bi bi-question-circle me-2"></i>
                            Ajuda
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-12 col-md-9 col-lg-10 main-content">
                <!-- Top Navigation -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                    <div class="container-fluid">
                        <span class="navbar-brand mb-0 h1">@yield('page-title', 'Dashboard')</span>
                        
                        <div class="navbar-nav ms-auto">
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-person-circle me-1"></i>
                                    Usuário
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Perfil</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Configurações</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#"><i class="bi bi-box-arrow-right me-2"></i>Sair</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
                
                <!-- Page Content -->
                <div class="container-fluid p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Axios for AJAX -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    @yield('scripts')
</body>
</html>
