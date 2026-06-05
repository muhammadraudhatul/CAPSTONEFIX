<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Student</title>

    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        * {
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: radial-gradient(ellipse at 30% 40%, #2a3f8f 0%, #1a2a6e 35%, #0d1540 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
        }

        .page-content {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 520px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        /* Back link */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.875rem;
            text-decoration: none;
            margin-bottom: 1.75rem;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: rgba(255, 255, 255, 0.9);
        }

        /* Card */
        .card {
            width: 100%;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 1.5rem;
            padding: 2.5rem 2.25rem;
            backdrop-filter: blur(16px);
        }

        /* Icon */
        .icon-wrap {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 1rem;
            background: linear-gradient(135deg, #60a5fa, #3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .icon-wrap i {
            font-size: 1.6rem;
            color: #fff;
        }

        h1 {
            color: #ffffff;
            font-size: 2rem;
            font-weight: 800;
            margin: 0 0 0.4rem;
        }

        .subtitle {
            color: rgba(255, 255, 255, 0.45);
            font-size: 0.9rem;
            margin: 0 0 1.75rem;
        }

        /* Errors */
        .errors {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.35);
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1.25rem;
            color: #fca5a5;
            font-size: 0.85rem;
        }

        /* Field */
        .field {
            margin-bottom: 1.1rem;
        }

        label {
            display: block;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.45rem;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.3);
            font-size: 1.1rem;
            pointer-events: none;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 0.75rem;
            padding: 0.8rem 0.9rem 0.8rem 2.6rem;
            color: #ffffff;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s, background 0.2s;
        }

        input::placeholder {
            color: rgba(255, 255, 255, 0.25);
        }

        input:focus {
            border-color: rgba(96, 165, 250, 0.5);
            background: rgba(255, 255, 255, 0.1);
        }

        .toggle-pw {
            position: absolute;
            right: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: rgba(255, 255, 255, 0.3);
            font-size: 1.1rem;
            padding: 0;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }

        .toggle-pw:hover {
            color: rgba(255, 255, 255, 0.6);
        }

        /* Submit button */
        .btn-submit {
            width: 100%;
            margin-top: 0.75rem;
            padding: 0.9rem;
            background: linear-gradient(90deg, #3b82f6, #06b6d4);
            border: none;
            border-radius: 0.875rem;
            color: #ffffff;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.15s;
        }

        .btn-submit:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Login link */
        .login-text {
            text-align: center;
            margin-top: 1.5rem;
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.875rem;
        }

        .login-text a {
            color: #60a5fa;
            font-weight: 700;
            text-decoration: none;
        }

        .login-text a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="page-content">

        <!-- Back -->
        <a href="/" class="back-link">
            <i class="ti ti-arrow-left"></i>
            Back to portal selection
        </a>

        <!-- Card -->
        <div class="card">

            <!-- Icon -->
            <div class="icon-wrap">
                <i class="ti ti-school"></i>
            </div>

            <h1>Create Account</h1>
            <p class="subtitle">Register to access the student portal</p>

            <!-- Errors -->
            @if ($errors->any())
                <div class="errors">
                    <ul style="margin:0; padding-left:1rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Full Name -->
                <div class="field">
                    <label>Full Name</label>
                    <div class="input-wrap">
                        <i class="ti ti-user input-icon"></i>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            placeholder="Enter your full name"
                        >
                    </div>
                </div>

                <!-- NIM -->
                <div class="field">
                    <label>Student ID (NIM)</label>
                    <div class="input-wrap">
                        <i class="ti ti-id-badge input-icon"></i>
                        <input
                            type="text"
                            name="nim"
                            value="{{ old('nim') }}"
                            required
                            placeholder="Enter your NIM"
                        >
                    </div>
                </div>

                <!-- Password -->
                <div class="field">
                    <label>Password</label>
                    <div class="input-wrap">
                        <i class="ti ti-lock input-icon"></i>
                        <input
                            type="password"
                            name="password"
                            id="passwordInput"
                            required
                            placeholder="Minimum 6 characters"
                        >
                        <button type="button" class="toggle-pw" onclick="togglePassword('passwordInput', 'eyeIcon1')">
                            <i id="eyeIcon1" class="ti ti-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="field">
                    <label>Confirm Password</label>
                    <div class="input-wrap">
                        <i class="ti ti-lock-check input-icon"></i>
                        <input
                            type="password"
                            name="password_confirmation"
                            id="passwordConfirmInput"
                            required
                            placeholder="Re-enter your password"
                        >
                        <button type="button" class="toggle-pw" onclick="togglePassword('passwordConfirmInput', 'eyeIcon2')">
                            <i id="eyeIcon2" class="ti ti-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-submit">Create Account</button>

                <!-- Login -->
                <p class="login-text">
                    Already have an account? <a href="{{ route('login') }}">Sign In</a>
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