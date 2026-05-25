<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Student</title>

    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
</head>

<body class="bg-[#f5f4ff] min-h-screen flex items-center justify-center py-10">

    <div class="w-full max-w-xl bg-white rounded-3xl shadow-xl overflow-hidden">

        <!-- Header -->
        <div class="bg-gradient-to-br from-green-600 to-teal-700 p-10 text-white relative">

            <a href="/"
               class="flex items-center gap-2 text-sm text-white hover:opacity-80 transition">
                ← Kembali
            </a>

            <div class="flex flex-col items-center mt-4">

                <i class="ti ti-school text-6xl text-white mb-4"></i>

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

                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <i class="ti ti-user text-lg"></i>
                        </span>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            placeholder="Masukkan nama lengkap"
                            class="w-full pl-10 rounded-xl border-gray-300 focus:border-teal-500 focus:ring-teal-500"
                        >
                    </div>
                </div>

                <!-- NIM -->
                <div class="mt-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        NIM
                    </label>

                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <i class="ti ti-id-badge text-lg"></i>
                        </span>
                        <input
                            type="text"
                            name="nim"
                            value="{{ old('nim') }}"
                            required
                            placeholder="Masukkan NIM"
                            class="w-full pl-10 rounded-xl border-gray-300 focus:border-teal-500 focus:ring-teal-500"
                        >
                    </div>
                </div>

                <!-- Password -->
                <div class="mt-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Password
                    </label>

                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <i class="ti ti-lock text-lg"></i>
                        </span>
                        <input
                            type="password"
                            name="password"
                            id="passwordInput"
                            required
                            placeholder="Minimal 8 karakter"
                            class="w-full pl-10 pr-10 rounded-xl border-gray-300 focus:border-teal-500 focus:ring-teal-500"
                        >
                        <button
                            type="button"
                            onclick="togglePassword('passwordInput', 'eyeIcon1')"
                            class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600"
                        >
                            <i id="eyeIcon1" class="ti ti-eye text-lg"></i>
                        </button>
                    </div>
                </div>

                <!-- Konfirmasi Password -->
                <div class="mt-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Konfirmasi Password
                    </label>

                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <i class="ti ti-lock-check text-lg"></i>
                        </span>
                        <input
                            type="password"
                            name="password_confirmation"
                            id="passwordConfirmInput"
                            required
                            placeholder="Ulangi password"
                            class="w-full pl-10 pr-10 rounded-xl border-gray-300 focus:border-teal-500 focus:ring-teal-500"
                        >
                        <button
                            type="button"
                            onclick="togglePassword('passwordConfirmInput', 'eyeIcon2')"
                            class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600"
                        >
                            <i id="eyeIcon2" class="ti ti-eye text-lg"></i>
                        </button>
                    </div>
                </div>

                <!-- Button -->
                <button
                    type="submit"
                    class="w-full mt-8 bg-gradient-to-r from-green-600 to-teal-700 text-white py-4 rounded-xl text-lg font-semibold hover:from-green-700 hover:to-teal-800 transition-all duration-200"
                >
                    Daftar
                </button>

                <!-- Login -->
                <p class="text-center text-gray-500 mt-8">
                    Sudah punya akun?
                    <a href="{{ route('login') }}"
                       class="text-teal-600 font-semibold hover:underline">
                        Login
                    </a>
                </p>

            </form>

        </div>

    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('ti-eye', 'ti-eye-off');
            } else {
                input.type = 'password';
                icon.classList.replace('ti-eye-off', 'ti-eye');
            }
        }
    </script>

</body>
</html>