<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Health Risk System')</title>
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --bg-light: #f8f9fa;
        }

        body {
            font-family: 'Sarabun', sans-serif;
            background-color: var(--bg-light);
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        #sidebar-wrapper {
            min-height: 100vh;
            width: var(--sidebar-width);
            background-color: var(--primary-color);
            transition: margin .25s ease-out;
            position: fixed;
            z-index: 1000;
        }

        #sidebar-wrapper .sidebar-heading {
            padding: 1.5rem 1rem;
            color: white;
            font-size: 1.2rem;
            font-weight: 600;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }

        #sidebar-wrapper .list-group-item {
            background-color: transparent;
            color: rgba(255,255,255,0.7);
            border: none;
            padding: 1rem 1.5rem;
            transition: all 0.3s;
        }

        #sidebar-wrapper .list-group-item:hover {
            color: white;
            background-color: rgba(255,255,255,0.1);
            padding-left: 2rem;
        }

        #sidebar-wrapper .list-group-item.active {
            color: white;
            background-color: var(--accent-color);
        }

        /* Page Content Styling */
        #page-content-wrapper {
            min-width: 100vw;
            padding-left: var(--sidebar-width);
            transition: padding .25s ease-out;
        }

        /* Topbar Styling */
        .navbar-custom {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            padding: 0.75rem 1.5rem;
        }

        /* Footer */
        footer {
            padding: 1.5rem;
            background-color: white;
            border-top: 1px solid #dee2e6;
            text-align: center;
            color: #6c757d;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            #sidebar-wrapper { margin-left: calc(var(--sidebar-width) * -1); }
            #page-content-wrapper { padding-left: 0; }
            #sidebar-wrapper.show { margin-left: 0; }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div id="sidebar-wrapper">
    <div class="sidebar-heading">
        <i class="fas fa-hand-holding-heart me-2"></i> HealthRiskScore
    </div>
    
    <div class="list-group list-group-flush mt-3">
        {{-- ส่วนของ Admin: แดชบอร์ด --}}
        @can('admin-only')
        <a href="{{ route('risk_scores.dashboard') }}" class="list-group-item list-group-item-action {{ Request::is('risk_scores/dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-line me-2"></i> แดชบอร์ด
        </a>
        @endcan

        {{-- ส่วนที่เห็นทุกคน --}}
        <a href="{{ route('risk_scores.index') }}" class="list-group-item list-group-item-action {{ Request::is('risk_score-list') || Request::is('risk_scores') ? 'active' : '' }}">
            <i class="fas fa-list-ul me-2"></i> รายการทั้งหมด
        </a>
        <a href="{{ route('risk_scores.create') }}" class="list-group-item list-group-item-action {{ Request::is('risk-score/create') || Request::is('/') ? 'active' : '' }}">
            <i class="fas fa-plus-circle me-2"></i> เพิ่มข้อมูลใหม่
        </a>

        <div class="px-4 py-2 mt-2 text-uppercase text-white-50" style="font-size: 0.7rem; letter-spacing: 1px; font-weight: bold;">
            Data Management
        </div>

        {{-- ส่วนของ Admin: Import/Export --}}
        @can('admin-only')
        <a href="{{ route('risk-scores.import-view') }}" class="list-group-item list-group-item-action {{ request()->is('risk-score/import-view') ? 'active' : '' }}">
            <i class="fas fa-file-upload me-2 text-info"></i> นำเข้าข้อมูล Excel
        </a>

        <a href="{{ route('risk_scores.export') }}" class="list-group-item list-group-item-action">
            <i class="fas fa-file-excel me-2 text-success"></i> ส่งออกข้อมูล Excel
        </a>
        @endcan
    </div>
</div>

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <!-- Topbar -->
            <nav class="navbar navbar-expand-lg navbar-light navbar-custom">
    <div class="container-fluid">
        <button class="btn btn-outline-secondary d-md-none" id="menu-toggle">
            <i class="fas fa-bars"></i>
        </button>
        
        <div class="ms-auto d-flex align-items-center">
            @auth
                <!-- แสดงเมื่อ Login แล้ว -->
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <span>{{ auth()->user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> โปรไฟล์</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="dropdown-item text-danger" type="submit">
                                    <i class="fas fa-sign-out-alt me-2"></i> ออกจากระบบ
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endauth

            @guest
                <!-- แสดงเมื่อยังไม่ได้ Login -->
                <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill px-4">
                    <i class="fas fa-sign-in-alt me-2"></i> เข้าสู่ระบบ
                </a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-primary rounded-pill px-4 ms-2">
                        สมัครสมาชิก
                    </a>
                @endif
            @endguest
        </div>
    </div>
</nav>

            <!-- Main Content -->
            <div class="container-fluid p-4" style="min-height: calc(100vh - 135px);">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @yield('content')
            </div>

            <!-- Footer -->
            <footer>
                &copy; {{ date('Y') }} Health Risk Assessment System. Develop by Anurak Butsri.
            </footer>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('menu-toggle')?.addEventListener('click', function() {
            document.getElementById('sidebar-wrapper').classList.toggle('show');
        });
    </script>
    @yield('scripts')
</body>
</html>