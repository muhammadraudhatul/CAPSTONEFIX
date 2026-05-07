<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>

    @vite('resources/css/app.css')
</head>

<body class="bg-[#f5f4ff] min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg overflow-hidden">

        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-8 text-white relative">

            <a href="/"
               class="absolute top-4 left-4 text-sm flex items-center gap-1 hover:opacity-80">
                ← Kembali
            </a>

            <div class="flex flex-col items-center">

                <div class="text-4xl mb-3">
                    🎓
                </div>

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

                    <input
                        type="text"
                        name="nim"
                        value="{{ old('nim') }}"
                        required
                        autofocus
                        class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                        placeholder="Masukkan NIM"
                    >
                </div>

                <!-- Password -->
                <div class="mt-5">

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        required
                        class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                        placeholder="Masukkan password"
                    >
                </div>

                <!-- Remember -->
                <div class="mt-4 flex items-center">

                    <input
                        type="checkbox"
                        name="remember"
                        class="rounded border-gray-300 text-purple-600 shadow-sm focus:ring-purple-500"
                    >

                    <span class="ml-2 text-sm text-gray-600">
                        Remember me
                    </span>

                </div>

                <!-- Button -->
                <button
                    type="submit"
                    class="w-full mt-6 bg-gradient-to-r from-blue-500 to-purple-600 text-white py-3 rounded-lg font-semibold hover:opacity-90 transition"
                >
                    Login
                </button>

                <!-- Register -->
                <p class="text-center text-sm text-gray-500 mt-6">
                    Belum punya akun?
                    <a href="{{ route('register') }}"
                       class="text-purple-600 font-semibold hover:underline">
                        Daftar
                    </a>
                </p>

            </form>

        </div>

    </div>

</body>
</html>