<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>

    @vite('resources/css/app.css')

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        * {
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: radial-gradient(ellipse at 70% 30%, #7c3aed 0%, transparent 55%),
                        radial-gradient(ellipse at 20% 80%, #6d28d9 0%, transparent 50%),
                        radial-gradient(ellipse at 50% 50%, #1e0a3c 0%, #150830 100%);
            background-color: #2d1157;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        /* ── Page wrapper (back link + card stacked) ── */
        .page-content {
            width: 100%;
            max-width: 480px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        /* ── Back link ── */
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

        .back-link:hover { color: rgba(255, 255, 255, 0.9); }

        /* ── Card ── */
        .card {
            width: 100%;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 1.5rem;
            padding: 2.5rem 2.25rem;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        /* ── Icon ── */
        .icon-wrap {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 1rem;
            background: linear-gradient(135deg, #a855f7, #ec4899);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.75rem;
            box-shadow: 0 8px 24px rgba(168, 85, 247, 0.4);
        }

        .icon-wrap svg {
            width: 26px;
            height: 26px;
            color: #fff;
        }

        /* ── Heading ── */
        h1 {
            color: #ffffff;
            font-size: 2rem;
            font-weight: 800;
            margin: 0 0 0.4rem;
            letter-spacing: -0.02em;
        }

        .subtitle {
            color: rgba(255, 255, 255, 0.45);
            font-size: 0.9rem;
            margin: 0 0 2rem;
        }

        /* ── Field ── */
        .field { margin-bottom: 1.25rem; }

        label {
            display: block;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .input-wrap { position: relative; }

        .input-icon {
            position: absolute;
            left: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.3);
            pointer-events: none;
            display: flex;
            align-items: center;
        }

        .input-icon svg { width: 18px; height: 18px; }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 0.75rem;
            padding: 0.8rem 2.6rem 0.8rem 2.6rem;
            color: #ffffff;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
        }

        input::placeholder { color: rgba(255, 255, 255, 0.25); }

        input:focus {
            border-color: rgba(168, 85, 247, 0.55);
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.12);
        }

        /* ── Eye toggle ── */
        .toggle-pw {
            position: absolute;
            right: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: rgba(255, 255, 255, 0.3);
            padding: 0;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }

        .toggle-pw:hover { color: rgba(255, 255, 255, 0.6); }
        .toggle-pw svg { width: 18px; height: 18px; }

        /* ── Submit button ── */
        .btn-submit {
            width: 100%;
            margin-top: 0.75rem;
            padding: 0.9rem;
            background: linear-gradient(90deg, #8b5cf6, #ec4899);
            border: none;
            border-radius: 0.875rem;
            color: #ffffff;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.15s;
            box-shadow: 0 8px 24px rgba(139, 92, 246, 0.35);
        }

        .btn-submit:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .btn-submit:active { transform: translateY(0); }

        /* ── Demo credentials box ── */
        .demo-box {
            margin-top: 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 0.85rem;
            padding: 0.9rem 1.25rem;
            text-align: center;
        }

        .demo-box-title {
            font-size: 0.75rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.4);
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 0.4rem;
        }

        .demo-box-creds {
            font-size: 0.88rem;
            color: rgba(255, 255, 255, 0.45);
        }

        .demo-box-creds strong {
            color: rgba(255, 255, 255, 0.85);
            font-weight: 700;
        }
    </style>
</head>

<body>

    <div class="page-content">

        <!-- Back link — di dalam flow, di atas card -->
        <a href="/" class="back-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to portal selection
        </a>

        <!-- Card -->
        <div class="card">

            <!-- Icon -->
            <div class="icon-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                </svg>
            </div>

            <h1>Admin Portal</h1>
            <p class="subtitle">Sign in to access the management dashboard</p>

            <form method="POST" action="/admin/login">
                @csrf

                <!-- Username -->
                <div class="field">
                    <label>Username</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </span>
                        <input type="text" name="nim" required placeholder="Enter your username">
                    </div>
                </div>

                <!-- Password -->
                <div class="field">
                    <label>Password</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </span>
                        <input type="password" name="password" id="passwordInput" required placeholder="Enter your password">
                        <button type="button" class="toggle-pw" onclick="togglePassword()">
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Sign In</button>

            </form>



        </div><!-- /.card -->

    </div><!-- /.page-content -->

    <script>
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>

</body>
</html>