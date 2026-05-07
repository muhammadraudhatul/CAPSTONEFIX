<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard</title>

    @vite('resources/css/app.css')
</head>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<body class="bg-[#f5f4ff] min-h-screen">

<div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-72 bg-white border-r flex flex-col justify-between">

        <div>

            <!-- Logo -->
            <div class="p-8 border-b">

                <h1 class="text-3xl font-bold text-gray-900">
                    Lab Management
                </h1>

                <p class="text-gray-500 mt-2">
                    Admin Portal
                </p>

            </div>

            <!-- Menu -->
                <nav class="p-4 space-y-2">

                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}"
                class="flex items-center gap-3 px-5 py-4 rounded-2xl bg-indigo-100 text-indigo-700 font-semibold">

                    📊 Dashboard

                </a>

                <!-- Inventory -->
                <div x-data="{ open: true }">

                    <button
                        @click="open = !open"
                        class="w-full flex items-center justify-between px-5 py-4 rounded-2xl hover:bg-gray-100 text-gray-700"
                    >

                        <div class="flex items-center gap-3">

                            📦 Inventory

                        </div>

                        <span x-text="open ? '⌄' : '›'"></span>

                    </button>

                    <!-- Dropdown -->
                    <div x-show="open"
                        x-transition
                        class="ml-6 mt-2 space-y-2">

                        <a href="{{ route('rooms.index') }}"
                        class="block px-4 py-3 rounded-xl bg-indigo-100 text-indigo-700 font-medium">

                            🚪 Ruangan

                        </a>

                        <a href="{{ route('items.index') }}"
                        class="block px-4 py-3 rounded-xl hover:bg-gray-100 text-gray-700">

                            🧪 Alat dan Bahan

                        </a>

                    </div>

                </div>

                <!-- Barang Masuk -->
                <a href="#"
                class="flex items-center gap-3 px-5 py-4 rounded-2xl hover:bg-gray-100 text-gray-700">

                    📥 Barang Masuk

                </a>

                <!-- Peminjaman -->
                <a href="#"
                class="flex items-center gap-3 px-5 py-4 rounded-2xl hover:bg-gray-100 text-gray-700">

                    📋 Peminjaman

                </a>

                <!-- Analytics -->
                <div x-data="{ open: false }">

                    <button
                        @click="open = !open"
                        class="w-full flex items-center justify-between px-5 py-4 rounded-2xl hover:bg-gray-100 text-gray-700"
                    >

                        <div class="flex items-center gap-3">

                            📈 Analytics

                        </div>

                        <span x-text="open ? '⌄' : '›'"></span>

                    </button>

                    <!-- Dropdown -->
                    <div x-show="open"
                        x-transition
                        class="ml-6 mt-2 space-y-2">

                        <a href="#"
                        class="block px-4 py-3 rounded-xl hover:bg-gray-100 text-gray-700">

                            🧪 Alat dan Bahan

                        </a>

                        <a href="#"
                        class="block px-4 py-3 rounded-xl hover:bg-gray-100 text-gray-700">

                            🚪 Ruangan

                        </a>

                    </div>

                </div>

            </nav>

        </div>

        <!-- Logout -->
        <div class="p-5">

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button
                    type="submit"
                    class="w-full bg-red-50 text-red-500 py-3 rounded-2xl hover:bg-red-100 transition"
                >
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