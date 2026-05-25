<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>

    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
</head>

<body class="bg-[#f5f4ff] min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg overflow-hidden">

        <!-- Header -->
        <div class="bg-gradient-to-br from-green-600 to-teal-700 p-8 text-white relative">

            <a href="/"
               class="flex items-center gap-2 text-sm text-white hover:opacity-80 transition">
                ← Kembali
            </a>

            <div class="flex flex-col items-center mt-4">

                <i class="ti ti-school text-5xl text-white mb-3"></i>

                <h1 class="text-2xl font-bold">
                    Student Login
                </h1>

                <p class="text-sm text-white/80 mt-1">
                    Masuk ke akun student Anda
                </p>

            </div>
        </div>

        <!-- Form -->
        <div class="p-8">

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    <ul class="text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- NIM -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        NIM
                    </label>

                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <i class="ti ti-user text-lg"></i>
                        </span>
                        <input
                            type="text"
                            name="nim"
                            value="{{ old('nim') }}"
                            required
                            autofocus
                            class="w-full pl-10 rounded-xl border-gray-300 focus:border-teal-500 focus:ring-teal-500"
                            placeholder="Masukkan NIM"
                        >
                    </div>
                </div>

                <!-- Password -->
                <div class="mt-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
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
                            class="w-full pl-10 pr-10 rounded-xl border-gray-300 focus:border-teal-500 focus:ring-teal-500"
                            placeholder="Masukkan password"
                        >
                        <button
                            type="button"
                            onclick="togglePassword()"
                            class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600"
                        >
                            <i id="eyeIcon" class="ti ti-eye text-lg"></i>
                        </button>
                    </div>
                </div>

                <!-- Button -->
                <button
                    type="submit"
                    class="w-full mt-6 bg-gradient-to-r from-green-600 to-teal-700 text-white py-3 rounded-xl font-semibold hover:from-green-700 hover:to-teal-800 transition-all duration-200"
                >
                    Login
                </button>

                <!-- Register -->
                <p class="text-center text-sm text-gray-500 mt-6">
                    Belum punya akun?
                    <a href="{{ route('register') }}"
                       class="text-teal-600 font-semibold hover:underline">
                        Daftar
                    </a>
                </p>

            </form>

        </div>

    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            const icon = document.getElementById('eyeIcon');
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