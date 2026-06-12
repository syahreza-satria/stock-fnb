<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-utensils"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Inventaris F&B</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ Request::routeIs('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Beranda</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Manajemen Inventaris
    </div>

    <!-- Nav Item - Ingredients -->
    <li class="nav-item {{ Request::routeIs('ingredients.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('ingredients.index') }}">
            <i class="fas fa-fw fa-leaf"></i>
            <span>Bahan Baku</span></a>
    </li>

    <!-- Nav Item - Recipes -->
    <li class="nav-item {{ Request::routeIs('recipes.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('recipes.index') }}">
            <i class="fas fa-fw fa-book-open"></i>
            <span>Resep</span></a>
    </li>

    <!-- Nav Item - POS / Sales Simulation -->
    <li class="nav-item {{ Request::routeIs('orders.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('orders.index') }}">
            <i class="fas fa-fw fa-cash-register"></i>
            <span>Simulasi Penjualan</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Analitik & Laporan
    </div>

    <!-- Nav Item - Reports -->
    <li class="nav-item {{ Request::routeIs('reports.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('reports.index') }}">
            <i class="fas fa-fw fa-chart-line"></i>
            <span>Log Pergerakan Stok</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    @if(Auth::user()->isAdmin())
    <!-- Heading: Administrasi -->
    <div class="sidebar-heading">
        Administrasi
    </div>

    <!-- Nav Item - User Management -->
    <li class="nav-item {{ Request::routeIs('users.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('users.index') }}">
            <i class="fas fa-fw fa-users-cog"></i>
            <span>Manajemen Pengguna</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">
    @endif


    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
