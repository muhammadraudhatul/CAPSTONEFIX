<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Management System</title>

    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
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
           class="bg-white w-60 rounded-2xl shadow-sm p-8 hover:shadow-md hover:scale-105 transition-all duration-200 text-center">

            <div class="w-20 h-20 mx-auto rounded-full bg-blue-500 flex items-center justify-center">
                <i class="ti ti-school text-4xl text-white"></i>
            </div>

            <h2 class="mt-5 text-xl font-bold text-gray-800">
                Student
            </h2>

            <p class="text-gray-500 text-sm mt-2">
                Akses form peminjaman
            </p>
        </a>

        <!-- Admin -->
        <a href="{{ route('admin.login') }}"
           class="bg-white w-60 rounded-2xl shadow-sm p-8 hover:shadow-md hover:scale-105 transition-all duration-200 text-center">

            <div class="w-20 h-20 mx-auto rounded-full bg-purple-500 flex items-center justify-center">
                <i class="ti ti-user-shield text-4xl text-white"></i>
            </div>

            <h2 class="mt-5 text-xl font-bold text-gray-800">
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