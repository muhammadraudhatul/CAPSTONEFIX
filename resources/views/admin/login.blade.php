<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>

    @vite('resources/css/app.css')
</head>

<body class="bg-[#f5f4ff] min-h-screen flex items-center justify-center">

    <a href="/"
       class="absolute top-8 left-8 text-green-600 font-semibold flex items-center gap-1 hover:text-green-700">
        ← Kembali ke Pilihan Role
    </a>

    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl p-10">

        <div class="text-center">

            <div class="flex justify-center mb-5">
                <div class="bg-green-100 rounded-full p-5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>

            <h1 class="text-4xl font-bold text-gray-900">
                Login Admin
            </h1>

            <p class="text-gray-500 mt-3">
                Masukkan kredensial Anda untuk mengakses dashboard
            </p>

        </div>

        <form method="POST" action="/admin/login" class="mt-10">

            @csrf

            <div>
                <label class="font-semibold text-gray-700">
                    Username
                </label>

                <div class="relative mt-3">
                    <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </span>
                    <input
                        type="text"
                        name="nim"
                        required
                        class="w-full pl-10 rounded-xl border-gray-300"
                        placeholder="Masukkan username"
                    >
                </div>
            </div>

            <div class="mt-6">
                <label class="font-semibold text-gray-700">
                    Password
                </label>

                <div class="relative mt-3">
                    <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </span>
                    <input
                        type="password"
                        name="password"
                        id="passwordInput"
                        required
                        class="w-full pl-10 pr-10 rounded-xl border-gray-300"
                        placeholder="Masukkan password"
                    >
                    <button
                        type="button"
                        onclick="togglePassword()"
                        class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600"
                    >
                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
            </div>

            <button
                type="submit"
                class="w-full mt-8 bg-gradient-to-r from-green-400 to-teal-500 text-white py-4 rounded-xl font-bold hover:from-green-500 hover:to-teal-600 transition-all duration-200"
            >
                Login
            </button>

        </form>

    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>

</body>
</html>