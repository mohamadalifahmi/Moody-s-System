<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Moody\'s')) - {{ config('app.name', 'Moody\'s') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.rtl.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="@auth sidebar-page @else auth-page @endauth">
    @auth
    <div class="wrapper">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <a href="{{ route('dashboard') }}">
                    <div class="brand-icon">
                        <svg viewBox="0 0 40 40" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <linearGradient id="sg" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#d4a853"/>
                                    <stop offset="100%" stop-color="#a07820"/>
                                </linearGradient>
                            </defs>
                            <path d="M20 3L35 10v13c0 8-15 14-15 14S5 31 5 23V10z" fill="url(#sg)"/>
                            <text x="20" y="26" text-anchor="middle" fill="#0c0c14" font-family="Arial,Helvetica,sans-serif" font-weight="900" font-size="18">M</text>
                        </svg>
                    </div>
                    <span class="brand-text">Moody's</span>
                    <small style="font-size:9px;color:var(--gold);opacity:0.6;position:absolute;bottom:20px;right:84px;letter-spacing:2px;font-weight:600;">MANAGEMENT</small>
                </a>
            </div>
            <div class="sidebar-divider"></div>
            <nav class="sidebar-nav">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="fas fa-chart-pie nav-icon"></i>
                            <span>لوحة التحكم</span>
                        </a>
                    </li>
                    <li class="nav-item has-submenu">
                        <a href="#" class="nav-link submenu-toggle">
                            <i class="fas fa-cash-register nav-icon"></i>
                            <span>المبيعات</span>
                            <i class="fas fa-chevron-down submenu-arrow"></i>
                        </a>
                        <ul class="submenu">
                            <li><a href="{{ route('sales.orders.create') }}" class="nav-link"><i class="fas fa-plus-circle"></i> طلب جديد</a></li>
                            <li><a href="{{ route('sales.orders.index') }}" class="nav-link"><i class="fas fa-list"></i> الطلبات</a></li>
                            <li><a href="{{ route('sales.sessions.index') }}" class="nav-link"><i class="fas fa-chair"></i> فترات المبيعات</a></li>
                        </ul>
                    </li>
                    <li class="nav-item has-submenu">
                        <a href="#" class="nav-link submenu-toggle">
                            <i class="fas fa-file-invoice-dollar nav-icon"></i>
                            <span>المصروفات</span>
                            <i class="fas fa-chevron-down submenu-arrow"></i>
                        </a>
                        <ul class="submenu">
                            <li><a href="{{ route('expenses.create') }}" class="nav-link"><i class="fas fa-plus-circle"></i> إضافة مصروف</a></li>
                            <li><a href="{{ route('expenses.index') }}" class="nav-link"><i class="fas fa-chart-bar"></i> المصروفات</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('debts.index') }}" class="nav-link {{ request()->routeIs('debts.*') ? 'active' : '' }}">
                            <i class="fas fa-balance-scale nav-icon"></i>
                            <span>الديون</span>
                        </a>
                    </li>
                    <li class="nav-item has-submenu">
                        <a href="#" class="nav-link submenu-toggle">
                            <i class="fas fa-warehouse nav-icon"></i>
                            <span>المخزون</span>
                            <i class="fas fa-chevron-down submenu-arrow"></i>
                        </a>
                        <ul class="submenu">
                            <li><a href="{{ route('inventory.products.index') }}" class="nav-link"><i class="fas fa-box"></i> المنتجات</a></li>
                            <li><a href="{{ route('inventory.categories.index') }}" class="nav-link"><i class="fas fa-tags"></i> التصنيفات</a></li>
                            <li><a href="{{ route('inventory.purchases.index') }}" class="nav-link"><i class="fas fa-truck-loading"></i> المشتريات</a></li>
                            <li><a href="{{ route('inventory.suppliers.index') }}" class="nav-link"><i class="fas fa-handshake"></i> الموردين</a></li>
                        </ul>
                    </li>
                    <li class="nav-item has-submenu">
                        <a href="#" class="nav-link submenu-toggle">
                            <i class="fas fa-file-invoice nav-icon"></i>
                            <span>الفواتير</span>
                            <i class="fas fa-chevron-down submenu-arrow"></i>
                        </a>
                        <ul class="submenu">
                            <li><a href="{{ route('invoicing.invoices.create') }}" class="nav-link"><i class="fas fa-file-invoice"></i> إنشاء فاتورة</a></li>
                            <li><a href="{{ route('invoicing.invoices.index') }}" class="nav-link"><i class="fas fa-receipt"></i> كل الفواتير</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                            <i class="fas fa-chart-line nav-icon"></i>
                            <span>التقارير</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                            <i class="fas fa-cog nav-icon"></i>
                            <span>الإعدادات</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <div class="sidebar-divider"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-link logout-btn">
                        <i class="fas fa-sign-out-alt nav-icon"></i>
                        <span>تسجيل الخروج</span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="main-content" id="mainContent">
            <header class="top-navbar">
                <div class="navbar-left">
                    <button class="sidebar-toggle" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <form class="search-box" method="GET" action="{{ route('search') }}">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" name="q" class="form-control" placeholder="بحث..." aria-label="بحث" value="{{ request('q') }}">
                    </form>
                </div>
                <div class="navbar-right">

                    <div class="dropdown user-dropdown">
                        <button class="btn user-btn" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar">
                                {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                            </div>
                            <span class="user-name d-none d-md-inline">{{ Auth::user()->name ?? 'مستخدم' }}</span>
                            <i class="fas fa-chevron-down ms-1"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a href="{{ route('profile') }}" class="dropdown-item"><i class="fas fa-user-circle"></i> الملف الشخصي</a></li>
                            <li><a href="{{ route('settings.index') }}" class="dropdown-item"><i class="fas fa-cog"></i> الإعدادات</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <main class="content-wrapper">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert" id="autoAlert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" id="autoAlert">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert" id="autoAlert">
                        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    @else
    <main class="content-wrapper">
        @yield('content')
    </main>
    @endauth

    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
</body>
</html>