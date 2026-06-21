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
            background: #D6E8E6;
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
            width: 240px;
            flex-shrink: 0;
            background: #D6E8E6;
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
            flex-direction: column;
            gap: 0.15rem;
            padding: 1.75rem 1.25rem 1.5rem;
        }

        .logo-text h1 {
            font-size: 1.05rem;
            font-weight: 800;
            color: #1a4a44;
            letter-spacing: -0.01em;
            line-height: 1.2;
        }

        .logo-text p {
            font-size: 0.65rem;
            color: #5a8a84;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-top: 0.1rem;
        }

        /* ── Nav ── */
        .sidebar-nav {
            padding: 0.5rem 0.75rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        /* base nav item */
        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.72rem 1rem;
            border-radius: 999px;
            color: #2E7D74;
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

        .nav-item i {
            font-size: 1.1rem;
            flex-shrink: 0;
            color: #2E7D74;
        }

        .nav-item:hover {
            background: rgba(46, 125, 116, 0.1);
            color: #1a4a44;
        }

        .nav-item:hover i {
            color: #1a4a44;
        }

        /* active state — solid teal pill */
        .nav-item.active {
            background: #2E7D74;
            color: #ffffff;
            font-weight: 600;
        }

        .nav-item.active i {
            color: #ffffff;
        }

        .nav-item.active:hover {
            background: #256860;
            color: #ffffff;
        }

        .nav-item.active:hover i {
            color: #ffffff;
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
            font-size: 0.75rem;
            color: #5a8a84;
            transition: transform 0.2s;
        }

        .nav-item.active .chevron {
            color: rgba(255,255,255,0.7);
        }

        /* submenu */
        .submenu {
            padding-left: 0.75rem;
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
            border-radius: 999px;
            color: #2E7D74;
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
            transition: background 0.18s, color 0.18s;
            font-family: 'Inter', sans-serif;
        }

        .nav-sub i {
            font-size: 1rem;
            flex-shrink: 0;
            color: #2E7D74;
        }

        .nav-sub:hover {
            background: rgba(46, 125, 116, 0.1);
            color: #1a4a44;
        }

        .nav-sub:hover i {
            color: #1a4a44;
        }

        .nav-sub.active {
            background: #2E7D74;
            color: #ffffff;
            font-weight: 600;
        }

        .nav-sub.active i {
            color: #ffffff;
        }

        /* ── Sign Out ── */
        .sidebar-footer {
            padding: 1rem 0.75rem 1.75rem;
        }

        .btn-signout {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.72rem 1rem;
            border-radius: 999px;
            color: #2E7D74;
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

        .btn-signout i {
            font-size: 1.1rem;
            color: #2E7D74;
        }

        .btn-signout:hover {
            background: rgba(46, 125, 116, 0.1);
            color: #1a4a44;
        }

        .btn-signout:hover i {
            color: #1a4a44;
        }

        /* ══════════════════════════════
           MAIN CONTENT
        ══════════════════════════════ */
        .main-content {
            flex: 1;
            min-width: 0;
            min-height: 100vh;
            background: #f0f7f6;
            padding: 2.5rem;
            color: #1a4a44;
            overflow-x: hidden;
        }

        /* ══════════════════════════════
        MOBILE SIDEBAR / OFF-CANVAS
        ══════════════════════════════ */

        [x-cloak] {
            display: none !important;
        }

        .mobile-topbar,
        .sidebar-backdrop,
        .sidebar-close {
            display: none;
        }

        @media (max-width: 900px) {
            .layout-wrapper {
                display: block;
                min-height: 100vh;
                width: 100%;
            }

            .mobile-topbar {
                position: sticky;
                top: 0;
                z-index: 45;
                display: flex;
                align-items: center;
                gap: 0.9rem;
                min-height: 68px;
                padding: 0.85rem 1rem;
                background: rgba(214, 232, 230, 0.92);
                border-bottom: 1px solid rgba(46, 125, 116, 0.12);
                backdrop-filter: blur(14px);
                -webkit-backdrop-filter: blur(14px);
                box-shadow: 0 8px 22px rgba(31, 42, 41, 0.06);
            }

            .mobile-menu-btn {
                width: 44px;
                height: 44px;
                border-radius: 15px;
                border: 1px solid rgba(46, 125, 116, 0.16);
                background: rgba(255, 255, 255, 0.62);
                color: #2E7D74;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                box-shadow: 0 4px 14px rgba(31, 42, 41, 0.05);
                transition: background 0.18s, transform 0.18s, color 0.18s;
            }

            .mobile-menu-btn:hover {
                background: rgba(46, 125, 116, 0.10);
                color: #1a4a44;
                transform: translateY(-1px);
            }

            .mobile-menu-btn i {
                font-size: 1.35rem;
            }

            .mobile-brand {
                display: flex;
                flex-direction: column;
                min-width: 0;
            }

            .mobile-brand p {
                font-size: 1rem;
                font-weight: 900;
                color: #1a4a44;
                letter-spacing: -0.02em;
                line-height: 1.1;
                margin: 0;
            }

            .mobile-brand span {
                font-size: 0.62rem;
                color: #5a8a84;
                font-weight: 800;
                letter-spacing: 0.12em;
                text-transform: uppercase;
                margin-top: 0.15rem;
            }

            .sidebar-backdrop {
                display: block;
                position: fixed;
                inset: 0;
                z-index: 55;
                background: rgba(31, 42, 41, 0.38);
                backdrop-filter: blur(4px);
                -webkit-backdrop-filter: blur(4px);
            }

            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                z-index: 60;
                width: min(82vw, 300px);
                height: 100vh;
                min-height: 100vh;
                transform: translateX(-105%);
                transition: transform 0.26s cubic-bezier(.2,.8,.2,1);
                box-shadow: 18px 0 44px rgba(31, 42, 41, 0.18);
                border-right: 1px solid rgba(255, 255, 255, 0.65);
            }

            .sidebar.sidebar-open {
                transform: translateX(0);
            }

            .sidebar-close {
                position: absolute;
                top: 1rem;
                right: 1rem;
                z-index: 2;
                width: 38px;
                height: 38px;
                border-radius: 14px;
                border: 1px solid rgba(46, 125, 116, 0.14);
                background: rgba(255, 255, 255, 0.62);
                color: #2E7D74;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: background 0.18s, color 0.18s, transform 0.18s;
            }

            .sidebar-close:hover {
                background: rgba(201, 122, 98, 0.12);
                color: #A85F49;
                transform: translateY(-1px);
            }

            .sidebar-close i {
                font-size: 1.25rem;
            }

            .sidebar-logo {
                padding-right: 4rem;
            }

            .main-content {
                width: 100%;
                min-height: calc(100vh - 68px);
                padding: 1.25rem 1rem;
            }
        }

        @media (max-width: 480px) {
            .sidebar {
                width: min(88vw, 300px);
            }

            .mobile-topbar {
                min-height: 64px;
                padding: 0.75rem 0.9rem;
            }

            .main-content {
                padding: 1rem 0.75rem;
            }
        }
    </style>

    @stack('styles')
</head>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<body>

    {{-- ══ LAYOUT WRAPPER ══ --}}
    <div
        class="layout-wrapper"
        x-data="{ sidebarOpen: false }"
        x-effect="document.body.style.overflow = sidebarOpen ? 'hidden' : ''"
    >
        <!-- ══ MOBILE TOPBAR ══ -->
        <header class="mobile-topbar">
            <button
                type="button"
                class="mobile-menu-btn"
                @click="sidebarOpen = true"
                aria-label="Open sidebar"
            >
                <i class="ti ti-menu-2"></i>
            </button>

            <div class="mobile-brand">
                <p>Laboratory</p>
                <span>Management System</span>
            </div>
        </header>

        <!-- ══ MOBILE BACKDROP ══ -->
        <div
            x-show="sidebarOpen"
            x-cloak
            x-transition.opacity
            @click="sidebarOpen = false"
            class="sidebar-backdrop"
        ></div>

        <!-- ══ SIDEBAR ══ -->
        <aside class="sidebar" :class="{ 'sidebar-open': sidebarOpen }">
            <button
                type="button"
                class="sidebar-close"
                @click="sidebarOpen = false"
                aria-label="Close sidebar"
            >
                <i class="ti ti-x"></i>
            </button>

            <div>
                <!-- Logo -->
                <div class="sidebar-logo">
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

    @stack('scripts')
</body>
</html>