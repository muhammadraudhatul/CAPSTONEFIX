<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mahasiswa</title>

    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

        * {
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
        }

        :root {
            --primary-teal: #4B958F;
            --primary-dark: #356F6B;
            --primary-soft: #E7EFED;
            --accent-gold: #D7BD62;
            --accent-soft: #F7F3E8;
            --coral: #C97A62;
            --coral-dark: #A85F49;
            --ink: #11172B;
            --muted: #65728A;
            --soft-muted: #8B96AA;
            --card: rgba(255, 255, 255, 0.92);
            --line: rgba(99, 115, 146, 0.14);
            --blue: #4C63F4;
            --blue-soft: rgba(76, 99, 244, 0.10);
            --sky: #5DADE2;
            --shadow-soft: 0 26px 70px rgba(25, 35, 60, 0.08);
            --shadow-card: 0 18px 44px rgba(32, 42, 74, 0.08);
            --shadow-blue: 0 18px 34px rgba(76, 99, 244, 0.20);
        }

        body {
            margin: 0;
            min-height: 100vh;
            background:
                radial-gradient(circle at 50% 0%, rgba(110, 139, 255, 0.16), transparent 34%),
                radial-gradient(circle at 8% 84%, rgba(75, 149, 143, 0.09), transparent 30%),
                radial-gradient(circle at 90% 72%, rgba(215, 189, 98, 0.10), transparent 26%),
                linear-gradient(180deg, #F8FBFF 0%, #F7F9FD 52%, #F5F7FC 100%);
            color: var(--ink);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(28, 42, 78, 0.045) 1px, transparent 1px),
                linear-gradient(90deg, rgba(28, 42, 78, 0.045) 1px, transparent 1px);
            background-size: 36px 36px;
            mask-image: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.72) 18%, rgba(0,0,0,0.72) 88%, transparent 100%);
            pointer-events: none;
            z-index: 0;
        }

        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background:
                linear-gradient(110deg, transparent 0%, transparent 44%, rgba(255,255,255,0.56) 46%, transparent 58%),
                radial-gradient(circle at 78% 12%, rgba(76, 99, 244, 0.055), transparent 22%);
            pointer-events: none;
            z-index: 0;
        }

        .page-content {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 1000px;
            display: flex;
            flex-direction: column;
            align-items: stretch;
        }

        /* Back link */
        .back-link {
            width: fit-content;
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            color: var(--muted);
            font-size: 0.88rem;
            font-weight: 800;
            text-decoration: none;
            margin-bottom: 1.25rem;
            padding: 0.55rem 0.85rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.68);
            border: 1px solid rgba(99, 115, 146, 0.12);
            box-shadow: 0 8px 22px rgba(28, 42, 78, 0.06);
            backdrop-filter: blur(10px);
            transition: color 0.2s, background 0.2s, transform 0.2s, border-color 0.2s;
        }

        .back-link:hover {
            color: var(--blue);
            background: rgba(255, 255, 255, 0.92);
            border-color: rgba(76, 99, 244, 0.16);
            transform: translateY(-1px);
        }

        .back-link i {
            font-size: 1rem;
        }

        /* Auth shell */
        .auth-shell {
            width: 100%;
            display: grid;
            grid-template-columns: 0.92fr 1fr;
            border-radius: 34px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid rgba(255, 255, 255, 0.88);
            box-shadow: var(--shadow-soft);
            backdrop-filter: blur(16px);
            position: relative;
        }

        .auth-shell::before {
            content: '';
            position: absolute;
            inset: 0 auto 0 0;
            width: 7px;
            background: linear-gradient(180deg, rgba(215, 189, 98, 0.88), rgba(75, 149, 143, 0.86), rgba(76, 99, 244, 0.78));
            pointer-events: none;
            z-index: 2;
        }

        .brand-panel {
            position: relative;
            min-height: 640px;
            padding: 3rem 2.7rem;
            background:
                radial-gradient(circle at 8% 10%, rgba(215, 189, 98, 0.15), transparent 26%),
                linear-gradient(145deg, rgba(247, 243, 232, 0.98), rgba(231, 239, 237, 0.88));
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

        .brand-panel::before {
            content: '';
            position: absolute;
            width: 350px;
            height: 350px;
            right: -138px;
            top: -110px;
            border-radius: 0 0 0 100%;
            background: rgba(255, 255, 255, 0.50);
            pointer-events: none;
        }

        .brand-panel::after {
            content: 'DAFTAR';
            position: absolute;
            left: 2.55rem;
            bottom: 1.75rem;
            color: rgba(53, 111, 107, 0.06);
            font-size: 4.2rem;
            line-height: 1;
            font-weight: 900;
            letter-spacing: 0.04em;
            pointer-events: none;
        }

        .brand-content,
        .brand-footer {
            position: relative;
            z-index: 1;
        }

        .system-badge {
            width: fit-content;
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.42rem 0.95rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.74);
            border: 1px solid rgba(53, 111, 107, 0.12);
            color: #4C5870;
            font-size: 0.74rem;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 2.1rem;
            box-shadow: 0 8px 20px rgba(31, 42, 41, 0.05);
        }

        .system-dot {
            width: 9px;
            height: 9px;
            border-radius: 999px;
            background: #52C96C;
            box-shadow: 0 0 0 4px rgba(82, 201, 108, 0.10);
        }

        .brand-title {
            font-size: clamp(2.1rem, 5vw, 3.35rem);
            font-weight: 900;
            letter-spacing: -0.065em;
            line-height: 1.05;
            color: var(--ink);
            margin: 0 0 1.1rem;
        }

        .brand-title span {
            display: inline-block;
            color: var(--blue);
        }

        .brand-copy {
            color: var(--muted);
            font-size: 0.98rem;
            font-weight: 700;
            line-height: 1.65;
            margin: 0;
            max-width: 360px;
        }

        .brand-list {
            display: grid;
            gap: 0.9rem;
            list-style: none;
            margin: 2.3rem 0 0;
            padding: 0;
        }

        .brand-list li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #3C475E;
            font-size: 0.92rem;
            font-weight: 800;
        }

        .brand-list-icon {
            width: 30px;
            height: 30px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--blue);
            background: rgba(76, 99, 244, 0.06);
            border: 1px solid rgba(76, 99, 244, 0.10);
            flex-shrink: 0;
        }

        .brand-list-icon i {
            font-size: 0.95rem;
        }

        .brand-footer {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            color: var(--soft-muted);
            font-size: 0.8rem;
            font-weight: 800;
        }

        .brand-footer-mark {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #43340E;
            background: linear-gradient(135deg, #E6D58C, #D7BD62);
            border: 1px solid rgba(255, 255, 255, 0.62);
            flex-shrink: 0;
        }

        .brand-footer-mark i {
            font-size: 1rem;
        }

        /* Card */
        .card {
            position: relative;
            width: 100%;
            background: rgba(255, 255, 255, 0.96);
            padding: 3rem 2.75rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            right: -82px;
            top: -98px;
            border-radius: 0 0 0 100%;
            background: linear-gradient(135deg, rgba(248,250,255,0.95), rgba(236,242,251,0.64));
            pointer-events: none;
        }

        .card-content {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 430px;
            margin: 0 auto;
        }

        /* Icon */
        .icon-wrap {
            width: 4.2rem;
            height: 4.2rem;
            border-radius: 1.05rem;
            background: linear-gradient(135deg, #5E78FF, var(--blue));
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.55rem;
            box-shadow: var(--shadow-blue);
        }

        .icon-wrap i {
            font-size: 1.85rem;
            color: #fff;
        }

        h1 {
            color: var(--ink);
            font-size: 2rem;
            font-weight: 900;
            margin: 0 0 0.45rem;
            letter-spacing: -0.055em;
            line-height: 1.1;
        }

        .subtitle {
            color: var(--muted);
            font-size: 0.92rem;
            font-weight: 700;
            line-height: 1.55;
            margin: 0 0 1.75rem;
        }

        /* Errors */
        .errors {
            background: rgba(201, 122, 98, 0.10);
            border: 1px solid rgba(201, 122, 98, 0.22);
            border-radius: 16px;
            padding: 0.85rem 1rem;
            margin-bottom: 1.25rem;
            color: var(--coral-dark);
            font-size: 0.85rem;
            font-weight: 700;
            line-height: 1.55;
            box-shadow: 0 4px 14px rgba(201, 122, 98, 0.05);
        }

        .errors ul {
            margin: 0;
            padding-left: 1rem;
        }

        /* Field */
        .field {
            margin-bottom: 1.1rem;
        }

        label {
            display: block;
            color: #3C475E;
            font-size: 0.78rem;
            font-weight: 900;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.07em;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 0.95rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--soft-muted);
            font-size: 1.1rem;
            pointer-events: none;
            z-index: 1;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            min-height: 50px;
            background: rgba(247, 243, 232, 0.72);
            border: 1px solid rgba(99, 115, 146, 0.14);
            border-radius: 16px;
            padding: 0.82rem 2.75rem 0.82rem 2.75rem;
            color: var(--ink);
            font-size: 0.92rem;
            font-weight: 700;
            outline: none;
            box-shadow: 0 4px 14px rgba(53, 111, 107, 0.04);
            transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
        }

        input::placeholder {
            color: var(--soft-muted);
            font-weight: 600;
        }

        input:focus {
            border-color: rgba(76, 99, 244, 0.34);
            background: #FFFFFF;
            box-shadow: 0 0 0 4px rgba(76, 99, 244, 0.10);
        }

        .toggle-pw {
            position: absolute;
            right: 0.95rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--soft-muted);
            font-size: 1.1rem;
            padding: 0;
            display: flex;
            align-items: center;
            transition: color 0.2s, transform 0.2s;
            z-index: 1;
        }

        .toggle-pw:hover {
            color: var(--blue);
            transform: translateY(-50%) scale(1.05);
        }

        /* Submit button */
        .btn-submit {
            width: 100%;
            min-height: 56px;
            margin-top: 0.85rem;
            padding: 0.9rem 1.2rem;
            background: linear-gradient(135deg, #5E78FF, var(--blue));
            border: none;
            border-radius: 16px;
            color: #ffffff;
            font-size: 1rem;
            font-weight: 900;
            cursor: pointer;
            box-shadow: 0 14px 26px rgba(76, 99, 244, 0.24);
            transition: transform 0.2s, box-shadow 0.2s, filter 0.2s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            filter: saturate(0.98);
            box-shadow: 0 18px 34px rgba(76, 99, 244, 0.28);
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        /* Login link */
        .login-text {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--muted);
            font-size: 0.875rem;
            font-weight: 700;
        }

        .login-text a {
            color: var(--blue);
            font-weight: 900;
            text-decoration: none;
        }

        .login-text a:hover {
            text-decoration: underline;
        }

        .register-note {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            margin-top: 1.35rem;
            padding: 0.95rem 1rem;
            border-radius: 16px;
            background: rgba(231, 239, 237, 0.62);
            border: 1px solid rgba(75, 149, 143, 0.10);
            color: var(--muted);
            font-size: 0.8rem;
            font-weight: 700;
            line-height: 1.5;
        }

        .register-note i {
            color: var(--primary-dark);
            font-size: 1.05rem;
            flex-shrink: 0;
            margin-top: 0.08rem;
        }

        @keyframes authFadeUp {
            from {
                opacity: 0;
                transform: translateY(14px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .back-link,
        .auth-shell {
            animation: authFadeUp 0.38s ease both;
        }

        .auth-shell {
            animation-delay: 0.08s;
        }

        @media (max-width: 860px) {
            body {
                justify-content: flex-start;
                padding: 1.25rem;
            }

            .page-content {
                max-width: 540px;
            }

            .auth-shell {
                grid-template-columns: 1fr;
                border-radius: 30px;
            }

            .brand-panel {
                min-height: auto;
                padding: 2.2rem 2rem;
            }

            .brand-panel::after {
                display: none;
            }

            .brand-list,
            .brand-footer {
                display: none;
            }

            .card {
                padding: 2.25rem 2rem;
            }

            .card-content {
                max-width: none;
            }
        }

        @media (max-width: 520px) {
            body {
                padding: 1rem;
            }

            .back-link {
                width: 100%;
                justify-content: center;
            }

            .brand-panel,
            .card {
                padding: 1.65rem 1.35rem;
            }

            .auth-shell {
                border-radius: 26px;
            }

            .icon-wrap {
                width: 3.75rem;
                height: 3.75rem;
            }

            h1 {
                font-size: 1.75rem;
            }
        }
    </style>
</head>

<body>

    <div class="page-content">

        <!-- Back -->
        <a href="/" class="back-link">
            <i class="ti ti-arrow-left"></i>
            Kembali ke pilihan role
        </a>

        <div class="auth-shell">

            <aside class="brand-panel">
                <div class="brand-content">
                    <div class="system-badge">
                        <span class="system-dot"></span>
                        Akses Mahasiswa Baru
                    </div>

                    <h2 class="brand-title">
                        Buat<br><span>Akun Mahasiswa</span>
                    </h2>
                    <p class="brand-copy">
                        Daftar sekali untuk mengakses layanan peminjaman laboratorium, mengajukan permintaan, dan mengelola aktivitas peminjaman alat.
                    </p>

                    <ul class="brand-list">
                        <li>
                            <span class="brand-list-icon"><i class="ti ti-id-badge"></i></span>
                            Gunakan NIM mahasiswa yang valid
                        </li>
                        <li>
                            <span class="brand-list-icon"><i class="ti ti-clipboard-check"></i></span>
                            Akses fitur peminjaman dan reservasi
                        </li>
                        <li>
                            <span class="brand-list-icon"><i class="ti ti-shield-check"></i></span>
                            Jaga kata sandi tetap pribadi dan aman
                        </li>
                    </ul>
                </div>

                <div class="brand-footer">
                    <span class="brand-footer-mark">
                        <i class="ti ti-school"></i>
                    </span>
                    Sistem Manajemen Laboratorium
                </div>
            </aside>

            <!-- Card -->
            <div class="card">
                <div class="card-content">

                    <!-- Icon -->
                    <div class="icon-wrap">
                        <i class="ti ti-user-plus"></i>
                    </div>

                    <h1>Buat Akun</h1>
                    <p class="subtitle">Daftar untuk mengakses</p>

                    <!-- Errors -->
                    @if ($errors->any())
                        <div class="errors">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <!-- Nama Lengkap -->
                        <div class="field">
                            <label>Nama Lengkap</label>
                            <div class="input-wrap">
                                <i class="ti ti-user input-icon"></i>
                                <input
                                    type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    required
                                    autofocus
                                    placeholder="Masukkan nama lengkap"
                                >
                            </div>
                        </div>

                        <!-- NIM -->
                        <div class="field">
                            <label>NIM Mahasiswa</label>
                            <div class="input-wrap">
                                <i class="ti ti-id-badge input-icon"></i>
                                <input
                                    type="text"
                                    name="nim"
                                    value="{{ old('nim') }}"
                                    required
                                    placeholder="Masukkan NIM"
                                >
                            </div>
                        </div>

                        <!-- Kata Sandi -->
                        <div class="field">
                            <label>Kata Sandi</label>
                            <div class="input-wrap">
                                <i class="ti ti-lock input-icon"></i>
                                <input
                                    type="password"
                                    name="password"
                                    id="passwordInput"
                                    required
                                    placeholder="Minimal 6 karakter"
                                >
                                <button type="button" class="toggle-pw" onclick="toggleKataSandi('passwordInput', 'eyeIcon1')">
                                    <i id="eyeIcon1" class="ti ti-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Confirm Kata Sandi -->
                        <div class="field">
                            <label>Confirm Kata Sandi</label>
                            <div class="input-wrap">
                                <i class="ti ti-lock-check input-icon"></i>
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    id="passwordConfirmInput"
                                    required
                                    placeholder="Masukkan ulang kata sandi"
                                >
                                <button type="button" class="toggle-pw" onclick="toggleKataSandi('passwordConfirmInput', 'eyeIcon2')">
                                    <i id="eyeIcon2" class="ti ti-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="btn-submit">Buat Akun</button>

                        <!-- Login -->
                        <p class="login-text">
                            Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>
                        </p>

                    </form>

                    <div class="register-note">
                        <i class="ti ti-info-circle"></i>
                        <span>Setelah registrasi, gunakan NIM dan kata sandi untuk masuk.</span>
                    </div>

                </div>
            </div>

        </div>

    </div>

    <script>
        function toggleKataSandi(inputId, iconId) {
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
