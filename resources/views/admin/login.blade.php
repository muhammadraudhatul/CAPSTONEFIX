<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>

    @vite('resources/css/app.css')
</head>

<body class="bg-[#f5f4ff] min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl p-10">

        <a href="/"
           class="text-purple-600 font-semibold">
            ← Kembali ke Pilihan Role
        </a>

        <div class="text-center mt-8">

            <div class="text-6xl mb-5">
                🔒
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

                <input
                    type="text"
                    name="nim"
                    required
                    class="w-full mt-3 rounded-xl border-gray-300"
                    placeholder="Masukkan username"
                >
            </div>

            <div class="mt-6">
                <label class="font-semibold text-gray-700">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    required
                    class="w-full mt-3 rounded-xl border-gray-300"
                    placeholder="Masukkan password"
                >
            </div>

            <button
                type="submit"
                class="w-full mt-8 bg-gradient-to-r from-blue-500 to-purple-600 text-white py-4 rounded-xl font-bold"
            >
                Login
            </button>

        </form>

        <div class="mt-8 bg-gray-50 rounded-2xl p-5 text-center text-gray-700">

            <p class="text-sm text-gray-500">
                Demo Credentials:
            </p>

            <p class="mt-2">
                <strong>Username:</strong> admin
            </p>

            <p>
                <strong>Password:</strong> admin123
            </p>

        </div>

    </div>

</body>
</html>