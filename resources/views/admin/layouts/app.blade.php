<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard</title>

    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            width: 100%;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #0f1629;
            min-height: 100vh;
        }

        /* ══════════════════════════════
           WRAPPER
        ══════════════════════════════ */
        .layout-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* ══════════════════════════════
           SIDEBAR
        ══════════════════════════════ */
        .sidebar {
            width: 260px;
            flex-shrink: 0;
            background: #141d3d;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 100vh;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        /* ── Logo ── */
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 1.6rem 1.4rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }

        .logo-icon {
            width: 46px;
            height: 46px;
            border-radius: 0.85rem;
            background: linear-gradient(135deg, #a855f7, #ec4899);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 6px 18px rgba(168,85,247,0.35);
        }

        .logo-icon i { font-size: 1.4rem; color: #fff; }

        .logo-text h1 {
            font-size: 0.95rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.01em;
            line-height: 1.2;
        }

        .logo-text p {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.4);
            margin-top: 0.1rem;
        }

        /* ── Nav ── */
        .sidebar-nav {
            padding: 1.25rem 0.85rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
        }

        /* base nav item */
        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.72rem 1rem;
            border-radius: 0.75rem;
            color: rgba(255,255,255,0.45);
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            transition: background 0.18s, color 0.18s;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            font-family: 'Inter', sans-serif;
        }

        .nav-item i { font-size: 1.15rem; flex-shrink: 0; }

        .nav-item:hover {
            background: rgba(255,255,255,0.06);
            color: rgba(255,255,255,0.85);
        }

        /* active state — gradient pill */
        .nav-item.active {
            background: linear-gradient(90deg, #8b5cf6, #ec4899);
            color: #fff;
            font-weight: 600;
            box-shadow: 0 4px 16px rgba(139,92,246,0.3);
        }

        .nav-item.active:hover {
            background: linear-gradient(90deg, #7c3aed, #db2777);
            color: #fff;
        }

        /* accordion toggle row */
        .nav-toggle {
            justify-content: space-between;
        }

        .nav-toggle-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .nav-toggle .chevron {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.3);
            transition: transform 0.2s;
        }

        /* submenu */
        .submenu {
            padding-left: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.1rem;
            margin-top: 0.1rem;
        }

        .nav-sub {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.6rem 1rem;
            border-radius: 0.65rem;
            color: rgba(255,255,255,0.38);
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
            transition: background 0.18s, color 0.18s;
            font-family: 'Inter', sans-serif;
        }

        .nav-sub i { font-size: 1rem; flex-shrink: 0; }

        .nav-sub:hover {
            background: rgba(255,255,255,0.05);
            color: rgba(255,255,255,0.75);
        }

        .nav-sub.active {
            color: rgba(255,255,255,0.9);
            background: rgba(139,92,246,0.15);
            font-weight: 600;
        }

        /* ── Sign Out ── */
        .sidebar-footer {
            padding: 1rem 0.85rem 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.06);
        }

        .btn-signout {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.72rem 1rem;
            border-radius: 0.75rem;
            color: rgba(255,255,255,0.4);
            font-size: 0.9rem;
            font-weight: 500;
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            transition: background 0.18s, color 0.18s;
        }

        .btn-signout i { font-size: 1.15rem; }

        .btn-signout:hover {
            background: rgba(239,68,68,0.1);
            color: #f87171;
        }

        /* ══════════════════════════════
           MAIN CONTENT
        ══════════════════════════════ */
        .main-content {
            flex: 1;
            min-width: 0;        /* ← penting: cegah overflow melampaui flex container */
            min-height: 100vh;
            background: #0f1629;
            padding: 2.5rem;
            color: #fff;
            overflow-x: hidden;
        }
    </style>

    @stack('styles')  {{-- ← TAMBAHKAN INI --}}
</head>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<body>

    {{-- ══ LAYOUT WRAPPER ══ --}}
    <div class="layout-wrapper">

        <!-- ══ SIDEBAR ══ -->
        <aside class="sidebar">

            <div>
                <!-- Logo -->
                <div class="sidebar-logo">
                    <div class="logo-icon">
                        <i class="ti ti-sparkles"></i>
                    </div>
                    <div class="logo-text">
                        <h1>Laboratory</h1>
                        <p>Management System</p>
                    </div>
                </div>

                <!-- Nav -->
                <nav class="sidebar-nav">

                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}"
                       class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="ti ti-layout-dashboard"></i>
                        Dashboard
                    </a>

                    <!-- Inventory -->
                    <div x-data="{ open: {{ request()->routeIs('rooms.*') || request()->routeIs('items.*') ? 'true' : 'true' }} }">
                        <button @click="open = !open"
                                class="nav-item nav-toggle {{ request()->routeIs('rooms.*') || request()->routeIs('items.*') ? 'active' : '' }}">
                            <span class="nav-toggle-left">
                                <i class="ti ti-package"></i>
                                Inventory
                            </span>
                            <i class="ti chevron" :class="open ? 'ti-chevron-down' : 'ti-chevron-right'"></i>
                        </button>

                        <div x-show="open" x-transition class="submenu">
                            <a href="{{ route('rooms.index') }}"
                               class="nav-sub {{ request()->routeIs('rooms.*') ? 'active' : '' }}">
                                <i class="ti ti-building"></i>
                                Ruangan
                            </a>
                            <a href="{{ route('items.index') }}"
                               class="nav-sub {{ request()->routeIs('items.*') ? 'active' : '' }}">
                                <i class="ti ti-flask"></i>
                                Alat dan Bahan
                            </a>
                        </div>
                    </div>

                    <!-- History -->
                    <a href="{{ route('item-histories.index') }}"
                       class="nav-item {{ request()->routeIs('item-histories.*') ? 'active' : '' }}">
                        <i class="ti ti-trending-up"></i>
                        History
                    </a>

                    <!-- Peminjaman -->
                    <a href="{{ route('admin.borrowings.index') }}"
                       class="nav-item {{ request()->routeIs('admin.borrowings.*') ? 'active' : '' }}">
                        <i class="ti ti-clipboard-list"></i>
                        Peminjaman
                    </a>

                    <!-- Analytics -->
                    <div x-data="{ open: {{ request()->routeIs('admin.analytics.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                                class="nav-item nav-toggle {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                            <span class="nav-toggle-left">
                                <i class="ti ti-chart-bar"></i>
                                Analytics
                            </span>
                            <i class="ti chevron" :class="open ? 'ti-chevron-down' : 'ti-chevron-right'"></i>
                        </button>

                        <div x-show="open" x-transition class="submenu">
                            <a href="{{ route('admin.analytics.items') }}"
                               class="nav-sub {{ request()->routeIs('admin.analytics.items') ? 'active' : '' }}">
                                <i class="ti ti-flask"></i>
                                Alat dan Bahan
                            </a>
                            <a href="{{ route('admin.analytics.rooms') }}"
                               class="nav-sub {{ request()->routeIs('admin.analytics.rooms') ? 'active' : '' }}">
                                <i class="ti ti-building"></i>
                                Ruangan
                            </a>
                        </div>
                    </div>

                    <!-- Kelola Akun -->
                    <div x-data="{ open: {{ request()->routeIs('admin.accounts.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                                class="nav-item nav-toggle {{ request()->routeIs('admin.accounts.*') ? 'active' : '' }}">
                            <span class="nav-toggle-left">
                                <i class="ti ti-users"></i>
                                Kelola Akun
                            </span>
                            <i class="ti chevron" :class="open ? 'ti-chevron-down' : 'ti-chevron-right'"></i>
                        </button>

                        <div x-show="open" x-transition class="submenu">
                            <a href="{{ route('admin.accounts.students') }}"
                               class="nav-sub {{ request()->routeIs('admin.accounts.students') ? 'active' : '' }}">
                                <i class="ti ti-school"></i>
                                Student
                            </a>
                            <a href="{{ route('admin.accounts.admins') }}"
                               class="nav-sub {{ request()->routeIs('admin.accounts.admins') ? 'active' : '' }}">
                                <i class="ti ti-user-cog"></i>
                                Admin
                            </a>
                        </div>
                    </div>

                </nav>
            </div>

            <!-- Sign Out -->
            <div class="sidebar-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-signout">
                        <i class="ti ti-logout"></i>
                        Sign Out
                    </button>
                </form>
            </div>

        </aside>

        <!-- ══ MAIN CONTENT ══ -->
        <main class="main-content">
            @yield('content')
        </main>

    </div>

    @stack('scripts')  {{-- ← TAMBAHKAN INI --}}
</body>
</html>