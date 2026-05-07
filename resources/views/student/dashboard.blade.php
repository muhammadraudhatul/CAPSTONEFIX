<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>

    @vite('resources/css/app.css')
</head>

<body class="bg-[#f5f4ff] min-h-screen">

    <!-- Navbar -->
    <div class="bg-white border-b px-8 py-5 flex justify-between items-center">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Student Dashboard
            </h1>

            <p class="text-gray-500 text-sm mt-1">
                Selamat datang, {{ auth()->user()->name }}
                ({{ auth()->user()->nim }})
            </p>
        </div>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button
                type="submit"
                class="text-red-500 border border-red-300 px-4 py-2 rounded-lg hover:bg-red-50 transition"
            >
                Logout
            </button>
        </form>

    </div>

    <!-- Content -->
    <div class="p-8">

        <!-- Button -->
        <a href="#"
           class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white px-5 py-3 rounded-xl shadow hover:opacity-90 transition">

            ＋ Peminjaman Baru
        </a>

        <!-- Active Borrow -->
        <div class="mt-8">

            <h2 class="font-semibold text-lg text-gray-800 mb-4">
                📦 Peminjaman Aktif
            </h2>

            <div class="bg-white rounded-2xl shadow-sm p-8 text-center text-gray-400">

                Tidak ada peminjaman aktif

            </div>

        </div>

        <!-- History -->
        <div class="mt-10">

            <h2 class="font-semibold text-lg text-gray-800 mb-4">
                🕘 History Peminjaman
            </h2>

            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">

                <table class="w-full">

                    <thead class="bg-gray-50 text-left text-sm text-gray-600">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Ruangan</th>
                            <th class="px-6 py-4">Waktu</th>
                            <th class="px-6 py-4">Alat</th>
                            <th class="px-6 py-4">Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <td colspan="5"
                                class="text-center text-gray-400 py-10">

                                Belum ada history peminjaman

                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</body>
</html>