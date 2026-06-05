<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratory Management System</title>

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
            background: radial-gradient(ellipse at top left, #3b1f6b 0%, #1e0f3a 40%, #0d0620 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        /* Subtle noise/grain overlay */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
        }

        .wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 1100px;
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            justify-content: center;
        }

        /* Badge */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 9999px;
            padding: 0.45rem 1.1rem;
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 2.5rem;
            backdrop-filter: blur(8px);
        }

        .badge i {
            color: #f5c518;
            font-size: 1rem;
        }

        /* Heading */
        h1 {
            text-align: center;
            font-size: clamp(2.4rem, 6vw, 3.8rem);
            font-weight: 800;
            color: #ffffff;
            line-height: 1.15;
            margin: 0 0 0.5rem;
        }

        h1 span {
            color: #b388ff;
        }

        .subtitle {
            text-align: center;
            color: rgba(255, 255, 255, 0.5);
            font-size: 1rem;
            margin: 0 0 3.5rem;
        }

        /* Cards Grid */
        .cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            width: 100%;
            max-width: 860px;
        }

        @media (max-width: 640px) {
            .cards {
                grid-template-columns: 1fr;
                max-width: 400px;
            }
        }

        /* Card */
        .card {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1.25rem;
            padding: 2rem 2rem 1.75rem;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            gap: 0;
            transition: background 0.25s, border-color 0.25s, transform 0.2s;
            backdrop-filter: blur(12px);
        }

        .card:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
        }

        /* Icon */
        .icon-wrap {
            width: 3.25rem;
            height: 3.25rem;
            border-radius: 0.875rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.4rem;
        }

        .icon-wrap.student {
            background: linear-gradient(135deg, #60a5fa, #3b82f6);
        }

        .icon-wrap.admin {
            background: linear-gradient(135deg, #f472b6, #a855f7);
        }

        .icon-wrap i {
            font-size: 1.5rem;
            color: #fff;
        }

        /* Card Title */
        .card-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: #ffffff;
            margin: 0 0 0.65rem;
        }

        /* Card Description */
        .card-desc {
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.6);
            line-height: 1.6;
            margin: 0 0 1.5rem;
            flex: 1;
        }

        /* CTA */
        .card-cta {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            color: #b388ff;
            font-size: 0.9rem;
            font-weight: 500;
            transition: gap 0.2s;
        }

        .card:hover .card-cta {
            gap: 0.6rem;
        }

        /* Footer */
        footer {
            position: relative;
            z-index: 1;
            padding: 1.5rem;
            color: rgba(255, 255, 255, 0.25);
            font-size: 0.8rem;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="wrapper">

    <!-- Badge -->
    <div class="badge">
        <i class="ti ti-sparkles"></i>
        Laboratory Management System
    </div>

    <!-- Heading -->
    <h1>Choose Your<br><span>Access Portal</span></h1>
    <p class="subtitle">Select your role to continue</p>

    <!-- Cards -->
    <div class="cards">

        <!-- Student -->
        <a href="{{ route('login') }}" class="card">
            <div class="icon-wrap student">
                <i class="ti ti-school"></i>
            </div>
            <h2 class="card-title">Student</h2>
            <p class="card-desc">Access loan requests and manage your laboratory equipment reservations</p>
            <span class="card-cta">Get Started <i class="ti ti-arrow-right"></i></span>
        </a>

        <!-- Admin -->
        <a href="{{ route('admin.login') }}" class="card">
            <div class="icon-wrap admin">
                <i class="ti ti-users"></i>
            </div>
            <h2 class="card-title">Admin</h2>
            <p class="card-desc">Manage inventory, track equipment, and oversee laboratory operations</p>
            <span class="card-cta">Get Started <i class="ti ti-arrow-right"></i></span>
        </a>

    </div>

</div>

<footer>
    &copy; 2026 Laboratory Management System. All rights reserved.
</footer>

</body>
</html>