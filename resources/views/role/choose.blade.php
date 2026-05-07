<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Management System</title>

    @vite('resources/css/app.css')
</head>
<body class="bg-[#f5f2ff] min-h-screen flex items-center justify-center">

<div class="text-center">

    <h1 class="text-4xl font-bold text-gray-800">
        Lab Management System
    </h1>

    <p class="text-gray-500 mt-2">
        Pilih role Anda untuk melanjutkan
    </p>

    <div class="flex gap-6 mt-10 justify-center">

        <!-- Student -->
        <a href="{{ route('login') }}"
           class="bg-white w-64 rounded-2xl shadow-md p-8 hover:scale-105 transition">

            <div class="w-16 h-16 mx-auto rounded-full bg-blue-500 flex items-center justify-center text-white text-3xl">
                🎓
            </div>

            <h2 class="mt-5 text-xl font-semibold">
                Student
            </h2>

            <p class="text-gray-500 text-sm mt-2">
                Akses form peminjaman
            </p>
        </a>

        <!-- Admin -->
        <a href="{{ route('admin.login') }}"
           class="bg-white w-64 rounded-2xl shadow-md p-8 hover:scale-105 transition">

            <div class="w-16 h-16 mx-auto rounded-full bg-purple-500 flex items-center justify-center text-white text-3xl">
                👨‍💼
            </div>

            <h2 class="mt-5 text-xl font-semibold">
                Admin
            </h2>

            <p class="text-gray-500 text-sm mt-2">
                Kelola inventory laboratorium
            </p>
        </a>

    </div>

</div>

</body>
</html>