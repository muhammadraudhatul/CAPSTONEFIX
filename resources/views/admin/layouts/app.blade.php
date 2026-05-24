<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard</title>

    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
</head>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<body class="bg-[#f5f4ff] min-h-screen">

<div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-60 bg-white border-r flex flex-col justify-between">

        <div>

            <!-- Logo -->
            <div class="px-6 py-6 border-b">
                <h1 class="text-xl font-bold text-gray-900">Lab Management</h1>
                <p class="text-sm text-gray-500 mt-1">Admin Portal</p>
            </div>

            <!-- Menu -->
            <nav class="p-4 space-y-1">

                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-100 text-gray-700 font-medium">
                    <i class="ti ti-layout-dashboard text-lg"></i>
                    Dashboard
                </a>

                <!-- Inventory Dropdown -->
                <div x-data="{ open: true }">

                    <button
                        @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-xl hover:bg-gray-100 text-gray-700 font-medium">
                        <div class="flex items-center gap-3">
                            <i class="ti ti-package text-lg"></i>
                            Inventory
                        </div>
                        <i class="ti text-sm transition-transform duration-200"
                           :class="open ? 'ti-chevron-down' : 'ti-chevron-right'"></i>
                    </button>

                    <div x-show="open" x-transition class="ml-4 mt-1 space-y-1">

                        <a href="{{ route('rooms.index') }}"
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-gray-100 text-gray-600 text-sm">
                            <i class="ti ti-building text-base"></i>
                            Ruangan
                        </a>

                        <a href="{{ route('items.index') }}"
                           class="flex items-center gap-3 px-4 py-2.5 rounded-xl bg-violet-50 text-violet-700 text-sm font-medium">
                            <i class="ti ti-flask text-base"></i>
                            Alat dan Bahan
                        </a>

                    </div>

                </div>

                <!-- History -->
                <a href="{{ route('item-histories.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-100 text-gray-700 font-medium">
                    <i class="ti ti-trending-up text-lg"></i>
                    History
                </a>

                <!-- Peminjaman -->
                <a href="{{ route('admin.borrowings.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-100 text-gray-700 font-medium">
                    <i class="ti ti-clipboard-list text-lg"></i>
                    Peminjaman
                </a>

                <!-- Analytics Dropdown -->
                <div x-data="{ open: false }">

                    <button
                        @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-xl hover:bg-gray-100 text-gray-700 font-medium">
                        <div class="flex items-center gap-3">
                            <i class="ti ti-chart-bar text-lg"></i>
                            Analytics
                        </div>
                        <i class="ti text-sm transition-transform duration-200"
                           :class="open ? 'ti-chevron-down' : 'ti-chevron-right'"></i>
                    </button>

                    <div x-show="open" x-transition class="ml-4 mt-1 space-y-1">
                        <a href="{{ route('admin.analytics.items') }}"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-gray-100 text-gray-600 text-sm">
                            <i class="ti ti-flask text-base"></i>
                            Alat dan Bahan
                        </a>

                        <a href="{{ route('admin.analytics.rooms') }}"
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-gray-100 text-gray-600 text-sm">
                            <i class="ti ti-building text-base"></i>
                            Ruangan
                        </a>

                    </div>

                </div>

            </nav>

        </div>

        <!-- Logout -->
        <div class="p-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    class="w-full bg-red-50 text-red-500 py-2.5 rounded-xl hover:bg-red-100 transition text-sm font-medium">
                    Logout
                </button>
            </form>
        </div>

    </aside>

    <!-- Content -->
    <main class="flex-1 p-10">
        @yield('content')
    </main>

</div>

</body>
</html>