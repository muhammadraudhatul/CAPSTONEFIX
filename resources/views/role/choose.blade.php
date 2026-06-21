<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen Laboratorium</title>

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
            --primary-light: #75B6B0;
            --primary-soft: #E7EFED;
            --accent-gold: #D7BD62;
            --accent-light: #E9DCA7;
            --accent-dark: #7F6B2A;
            --coral: #C97A62;
            --coral-dark: #A85F49;
            --cream: #F7F3E8;
            --surface-base: #EDF2F1;
            --surface-card: #FAFBFA;
            --ink: #1F2A29;
            --muted: #657675;
            --soft-muted: #8A9997;
            --line: rgba(53, 111, 107, 0.13);
            --line-light: rgba(255, 255, 255, 0.82);
            --shadow-sm: 0 2px 8px rgba(31, 42, 41, 0.05);
            --shadow-card: 0 10px 28px rgba(53, 111, 107, 0.07);
            --shadow-soft: 0 18px 44px rgba(31, 42, 41, 0.07);
            --shadow-teal: 0 18px 36px rgba(75, 149, 143, 0.16);
            --shadow-gold: 0 18px 36px rgba(215, 189, 98, 0.16);
            --shadow-coral: 0 18px 36px rgba(201, 122, 98, 0.14);
        }

        body {
            margin: 0;
            min-height: 100vh;
            background:
                radial-gradient(circle at 12% 12%, rgba(215, 189, 98, 0.12), transparent 26%),
                radial-gradient(circle at 86% 18%, rgba(75, 149, 143, 0.12), transparent 28%),
                radial-gradient(circle at 50% 94%, rgba(201, 122, 98, 0.08), transparent 30%),
                linear-gradient(135deg, #F3F6F5 0%, var(--surface-base) 50%, #EEF3F1 100%);
            color: var(--ink);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(53, 111, 107, 0.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(53, 111, 107, 0.035) 1px, transparent 1px);
            background-size: 38px 38px;
            mask-image: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.65) 18%, rgba(0,0,0,0.65) 88%, transparent 100%);
            pointer-events: none;
            z-index: 0;
        }

        body::after {
            content: '';
            position: fixed;
            inset: auto -120px -180px auto;
            width: 430px;
            height: 430px;
            border-radius: 999px;
            background: rgba(247, 243, 232, 0.45);
            border: 1px solid rgba(255, 255, 255, 0.56);
            pointer-events: none;
            z-index: 0;
        }

        .wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 1180px;
            padding: 3rem 1.5rem 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            justify-content: center;
        }

        .portal-shell {
            width: 100%;
            position: relative;
            padding: 2.2rem;
            border-radius: 38px;
            background: rgba(250, 251, 250, 0.48);
            border: 1px solid rgba(255, 255, 255, 0.74);
            box-shadow: 0 26px 70px rgba(31, 42, 41, 0.065);
            overflow: hidden;
            backdrop-filter: blur(14px);
        }

        .portal-shell::before,
        .portal-shell::after {
            content: '';
            position: absolute;
            border-radius: 28px;
            border: 1px solid rgba(53, 111, 107, 0.05);
            background: rgba(247, 243, 232, 0.24);
            pointer-events: none;
        }

        .portal-shell::before {
            width: 154px;
            height: 214px;
            right: -74px;
            top: 120px;
            transform: rotate(-8deg);
        }

        .portal-shell::after {
            width: 112px;
            height: 150px;
            left: -52px;
            bottom: 118px;
            transform: rotate(10deg);
        }

        .hero,
        .cards {
            position: relative;
            z-index: 1;
        }

        /* Badge */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid rgba(75, 149, 143, 0.13);
            border-radius: 9999px;
            padding: 0.42rem 1rem;
            color: #4C8A85;
            font-size: 0.74rem;
            font-weight: 900;
            letter-spacing: 0.10em;
            text-transform: uppercase;
            margin-bottom: 1.45rem;
            box-shadow: 0 8px 22px rgba(53, 111, 107, 0.07);
            backdrop-filter: blur(10px);
        }

        .badge i {
            width: 9px;
            height: 9px;
            border-radius: 999px;
            background: #52C96C;
            color: transparent;
            font-size: 0;
            box-shadow: 0 0 0 4px rgba(82, 201, 108, 0.10);
        }

        .hero {
            text-align: center;
            max-width: 780px;
            margin: 0 auto 3.25rem;
        }

        /* Heading */
        h1 {
            text-align: center;
            font-size: clamp(2.45rem, 5.4vw, 4.2rem);
            font-weight: 900;
            color: var(--ink);
            line-height: 1.05;
            letter-spacing: -0.068em;
            margin: 0 0 1.15rem;
            text-shadow: 2px 2px 0 rgba(215, 189, 98, 0.18);
        }

        h1 span {
            display: inline-block;
            color: var(--primary-dark);
            letter-spacing: -0.07em;
        }

        .subtitle {
            text-align: center;
            color: var(--muted);
            font-size: clamp(0.96rem, 1.6vw, 1.12rem);
            font-weight: 750;
            line-height: 1.72;
            max-width: 650px;
            margin: 0 auto;
        }

        /* Cards Grid */
        .cards {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1.65rem;
            width: 100%;
            max-width: 980px;
            margin: 0 auto;
        }

        /* Card */
        .card {
            position: relative;
            min-height: 470px;
            background: linear-gradient(180deg, rgba(250,251,250,0.98), rgba(247, 243, 232, 0.88));
            border: 1px solid rgba(255, 255, 255, 0.82);
            border-left: 7px solid var(--primary-teal);
            border-radius: 30px;
            padding: 2.35rem 2.2rem 2rem;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: var(--shadow-soft);
            backdrop-filter: blur(14px);
            transition:
                border-color 0.24s ease,
                transform 0.24s ease,
                box-shadow 0.24s ease,
                background 0.24s ease;
        }

        .admin-card {
            border-left-color: var(--coral);
        }

        .card::before {
            content: '';
            position: absolute;
            width: 310px;
            height: 310px;
            right: -66px;
            top: -92px;
            border-radius: 0 0 0 100%;
            background: linear-gradient(135deg, rgba(231,239,237,0.76), rgba(250,251,250,0.38));
            pointer-events: none;
            transition: opacity 0.24s ease, transform 0.24s ease;
        }

        .admin-card::before {
            background: linear-gradient(135deg, rgba(247, 243, 232, 0.82), rgba(242, 221, 213, 0.52));
        }

        .card::after {
            content: '';
            position: absolute;
            inset: auto 0 0 0;
            height: 8px;
            opacity: 0.92;
            pointer-events: none;
        }

        .card.student-card::after {
            background: linear-gradient(90deg, rgba(215,189,98,0.92), rgba(75,149,143,0.90), rgba(117,182,176,0.72));
        }

        .card.admin-card::after {
            background: linear-gradient(90deg, rgba(215,189,98,0.92), rgba(201,122,98,0.78), rgba(75,149,143,0.52));
        }

        .card:hover {
            transform: translateY(-4px);
            background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(247, 243, 232, 0.92));
        }

        .card.student-card:hover {
            border-color: rgba(75, 149, 143, 0.18);
            box-shadow: var(--shadow-teal);
        }

        .card.admin-card:hover {
            border-color: rgba(201, 122, 98, 0.18);
            box-shadow: var(--shadow-coral);
        }

        .card:hover::before {
            transform: translate(-7px, 7px);
            opacity: 0.94;
        }

        .card-inner {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        /* Access Pill */
        .access-pill {
            position: absolute;
            top: 2.3rem;
            right: 2.1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 30px;
            padding: 0.28rem 0.88rem;
            border-radius: 999px;
            background: rgba(75, 149, 143, 0.10);
            border: 1px solid rgba(75, 149, 143, 0.13);
            color: var(--primary-dark);
            font-size: 0.7rem;
            font-weight: 900;
            line-height: 1;
            letter-spacing: 0.04em;
            transition: background 0.2s, color 0.2s, border-color 0.2s;
        }

        .admin-card .access-pill {
            background: rgba(201, 122, 98, 0.10);
            border-color: rgba(201, 122, 98, 0.13);
            color: var(--coral-dark);
        }

        .student-card:hover .access-pill {
            background: rgba(75, 149, 143, 0.15);
            border-color: rgba(75, 149, 143, 0.22);
        }

        .admin-card:hover .access-pill {
            background: rgba(201, 122, 98, 0.15);
            border-color: rgba(201, 122, 98, 0.22);
        }

        /* Icon */
        .icon-wrap {
            width: 4.05rem;
            height: 4.05rem;
            border-radius: 1.08rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2.05rem;
            color: #fff;
            box-shadow: 0 14px 26px rgba(75, 149, 143, 0.16);
            transition: transform 0.22s ease, box-shadow 0.22s ease;
        }

        .icon-wrap.student {
            background: linear-gradient(135deg, #7DB4AF, var(--primary-teal));
        }

        .icon-wrap.admin {
            background: linear-gradient(135deg, #D8987E, var(--coral));
            box-shadow: 0 14px 26px rgba(201, 122, 98, 0.16);
        }

        .card:hover .icon-wrap {
            transform: translateY(-2px);
        }

        .student-card:hover .icon-wrap {
            box-shadow: 0 18px 34px rgba(75, 149, 143, 0.22);
        }

        .admin-card:hover .icon-wrap {
            box-shadow: 0 18px 34px rgba(201, 122, 98, 0.20);
        }

        .icon-wrap i {
            font-size: 1.75rem;
            color: #fff;
        }

        /* Card Title */
        .card-title {
            font-size: clamp(1.55rem, 2.3vw, 1.95rem);
            font-weight: 900;
            color: var(--ink);
            letter-spacing: -0.055em;
            line-height: 1.12;
            margin: 0 0 0.95rem;
            transition: color 0.22s ease;
        }

        .student-card:hover .card-title {
            color: var(--primary-dark);
        }

        .admin-card:hover .card-title {
            color: var(--coral-dark);
        }

        /* Card Description */
        .card-desc {
            font-size: 0.94rem;
            color: var(--muted);
            font-weight: 750;
            line-height: 1.65;
            margin: 0 0 1.75rem;
        }

        .features {
            display: grid;
            gap: 0.85rem;
            margin: 0 0 2.1rem;
            padding: 0;
            list-style: none;
        }

        .features li {
            display: flex;
            align-items: center;
            gap: 0.72rem;
            color: #3C4B49;
            font-size: 0.9rem;
            font-weight: 850;
            line-height: 1.35;
        }

        .feature-icon {
            width: 31px;
            height: 31px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-dark);
            background: rgba(75, 149, 143, 0.09);
            border: 1px solid rgba(75, 149, 143, 0.11);
            flex-shrink: 0;
        }

        .admin-card .feature-icon {
            color: var(--coral-dark);
            background: rgba(201, 122, 98, 0.09);
            border-color: rgba(201, 122, 98, 0.11);
        }

        .feature-icon i {
            font-size: 0.98rem;
        }

        .card-spacer {
            flex: 1;
        }

        /* CTA */
        .card-cta {
            width: 100%;
            min-height: 54px;
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            border-radius: 999px;
            padding: 0 1.35rem;
            background: var(--ink);
            color: #FFFFFF;
            font-size: 0.92rem;
            font-weight: 900;
            box-shadow: 0 10px 20px rgba(31, 42, 41, 0.13);
            transition: background 0.22s ease, transform 0.22s ease, box-shadow 0.22s ease;
        }

        .card-cta i {
            font-size: 1.2rem;
            transition: transform 0.22s ease;
        }

        .student-card:hover .card-cta {
            background: linear-gradient(135deg, #7DB4AF, var(--primary-teal));
            box-shadow: 0 14px 26px rgba(75, 149, 143, 0.22);
        }

        .admin-card:hover .card-cta {
            background: linear-gradient(135deg, #D8987E, var(--coral));
            box-shadow: 0 14px 26px rgba(201, 122, 98, 0.20);
        }

        .card:hover .card-cta {
            transform: translateY(-1px);
        }

        .card:hover .card-cta i {
            transform: translateX(5px);
        }

        /* Footer */
        footer {
            position: relative;
            z-index: 1;
            padding: 1.15rem 1.5rem 1.4rem;
            color: var(--soft-muted);
            font-size: 0.8rem;
            font-weight: 800;
            text-align: center;
        }

        @keyframes roleFadeUp {
            from {
                opacity: 0;
                transform: translateY(14px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .portal-shell,
        footer {
            animation: roleFadeUp 0.38s ease both;
        }

        footer {
            animation-delay: 0.08s;
        }

        @media (max-width: 900px) {
            body {
                justify-content: flex-start;
            }

            .wrapper {
                padding: 2rem 1rem 1rem;
                justify-content: flex-start;
            }

            .portal-shell {
                padding: 1.6rem;
                border-radius: 30px;
            }

            .hero {
                margin-bottom: 2.25rem;
            }

            .cards {
                grid-template-columns: 1fr;
                max-width: 520px;
                gap: 1.15rem;
            }

            .card {
                min-height: auto;
            }
        }

        @media (max-width: 640px) {
            .wrapper {
                padding-top: 1rem;
            }

            .portal-shell {
                padding: 1.1rem;
                border-radius: 24px;
            }

            .badge {
                margin-bottom: 1rem;
            }

            h1 {
                font-size: clamp(2.05rem, 11vw, 2.65rem);
                letter-spacing: -0.06em;
            }

            .subtitle {
                font-size: 0.92rem;
                line-height: 1.6;
            }

            .card {
                padding: 2rem 1.35rem 1.55rem;
                border-radius: 24px;
            }

            .access-pill {
                position: static;
                width: fit-content;
                margin-bottom: 1.1rem;
                order: -1;
            }

            .icon-wrap {
                width: 3.6rem;
                height: 3.6rem;
                margin-bottom: 1.45rem;
            }

            .card-desc {
                margin-bottom: 1.45rem;
            }

            .features {
                gap: 0.7rem;
                margin-bottom: 1.55rem;
            }

            .features li {
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>

<div class="wrapper">

    <div class="portal-shell">

        <div class="hero">
            <!-- Badge -->
            <div class="badge">
                <i class="ti ti-sparkles"></i>
                Sistem Aktif
            </div>

            <!-- Heading -->
            <h1>Selamat Datang di<br><span>Laboratorium</span></h1>
            <p class="subtitle">Pilih jenis akses sesuai peran untuk masuk ke dashboard dan fitur yang tersedia.</p>
        </div>

        <!-- Cards -->
        <div class="cards">

            <!-- Student -->
            <a href="{{ route('login') }}" class="card student-card">
                <div class="card-inner">
                    <span class="access-pill">Akses Mahasiswa</span>

                    <div class="icon-wrap student">
                        <i class="ti ti-school"></i>
                    </div>

                    <h2 class="card-title">Mahasiswa</h2>
                    <p class="card-desc">Masuk ke ruang kerja mahasiswa untuk mengajukan peminjaman, memantau reservasi, dan melihat aktivitas laboratorium.</p>

                    <ul class="features">
                        <li>
                            <span class="feature-icon"><i class="ti ti-book"></i></span>
                            Akses layanan peminjaman laboratorium
                        </li>
                        <li>
                            <span class="feature-icon"><i class="ti ti-trophy"></i></span>
                            Pantau status peminjaman
                        </li>
                        <li>
                            <span class="feature-icon"><i class="ti ti-users"></i></span>
                            Kelola reservasi dan pengajuan
                        </li>
                    </ul>

                    <div class="card-spacer"></div>

                    <span class="card-cta">
                        Masuk sebagai Mahasiswa
                        <i class="ti ti-arrow-right"></i>
                    </span>
                </div>
            </a>

            <!-- Admin -->
            <a href="{{ route('admin.login') }}" class="card admin-card">
                <div class="card-inner">
                    <span class="access-pill">Akses Admin</span>

                    <div class="icon-wrap admin">
                        <i class="ti ti-shield-check"></i>
                    </div>

                    <h2 class="card-title">Administrator</h2>
                    <p class="card-desc">Kelola sistem laboratorium, pantau inventaris, lihat analitik, dan atur operasional.</p>

                    <ul class="features">
                        <li>
                            <span class="feature-icon"><i class="ti ti-settings"></i></span>
                            Kelola pengaturan sistem
                        </li>
                        <li>
                            <span class="feature-icon"><i class="ti ti-chart-bar"></i></span>
                            Lihat analitik platform
                        </li>
                        <li>
                            <span class="feature-icon"><i class="ti ti-shield"></i></span>
                            Kelola akses administrasi
                        </li>
                    </ul>

                    <div class="card-spacer"></div>

                    <span class="card-cta">
                        Masuk sebagai Admin
                        <i class="ti ti-arrow-right"></i>
                    </span>
                </div>
            </a>

        </div>

    </div>

</div>

<footer>
    &copy; 2026 Sistem Manajemen Laboratorium. Hak cipta dilindungi.
</footer>

</body>
</html>
