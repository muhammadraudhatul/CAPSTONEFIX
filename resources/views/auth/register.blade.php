<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Student</title>

    @vite('resources/css/app.css')
</head>

<body class="bg-[#f5f4ff] min-h-screen flex items-center justify-center py-10">

    <div class="w-full max-w-xl bg-white rounded-3xl shadow-xl overflow-hidden">

        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-10 text-white relative">

            <a href="/"
               class="absolute top-5 left-5 text-sm flex items-center gap-1 hover:opacity-80">
                ← Kembali
            </a>

            <div class="flex flex-col items-center">

                <div class="text-5xl mb-4">
                    🎓
                </div>

                <h1 class="text-4xl font-bold">
                    Daftar Akun Student
                </h1>

                <p class="mt-3 text-white/80">
                    Buat akun baru untuk mengakses sistem
                </p>

            </div>
        </div>

        <!-- Form -->
        <div class="p-10">

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl">
                    <ul class="text-sm list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Nama -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Lengkap
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        placeholder="Masukkan nama lengkap"
                        class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                    >
                </div>

                <!-- NIM -->
                <div class="mt-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        NIM
                    </label>

                    <input
                        type="text"
                        name="nim"
                        value="{{ old('nim') }}"
                        required
                        placeholder="Masukkan NIM"
                        class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                    >
                </div>

                <!-- Password -->
                <div class="mt-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        required
                        placeholder="Minimal 6 karakter"
                        class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                    >
                </div>

                <!-- Konfirmasi Password -->
                <div class="mt-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Konfirmasi Password
                    </label>

                    <input
                        type="password"
                        name="password_confirmation"
                        required
                        placeholder="Ulangi password"
                        class="w-full rounded-xl border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                    >
                </div>

                <!-- Button -->
                <button
                    type="submit"
                    class="w-full mt-8 bg-gradient-to-r from-blue-500 to-purple-600 text-white py-4 rounded-xl text-lg font-semibold hover:opacity-90 transition"
                >
                    Daftar
                </button>

                <!-- Login -->
                <p class="text-center text-gray-500 mt-8">
                    Sudah punya akun?
                    <a href="{{ route('login') }}"
                       class="text-purple-600 font-semibold hover:underline">
                        Login
                    </a>
                </p>

            </form>

        </div>

    </div>

</body>
</html>