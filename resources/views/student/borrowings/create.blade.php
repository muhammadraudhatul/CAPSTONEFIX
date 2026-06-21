<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Peminjaman Baru</title>

    @vite('resources/css/app.css')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

        *, *::before, *::after {
            box-sizing: border-box;
        }

        :root {
            --primary-teal: #4B958F;
            --primary-light: #75B6B0;
            --primary-dark: #356F6B;
            --primary-bg: #E7EFED;
            --accent-gold: #D7BD62;
            --accent-light: #E9DCA7;
            --coral: #C97A62;
            --coral-dark: #A85F49;
            --blue: #4C63F4;
            --blue-soft: rgba(76, 99, 244, 0.10);
            --cream: #F7F3E8;
            --surface-base: #EDF2F1;
            --surface-card: #FAFBFA;
            --text-primary: #1F2A29;
            --text-muted: #657675;
            --text-soft: #8A9997;
            --line: rgba(53, 111, 107, 0.13);
            --line-light: rgba(255, 255, 255, 0.82);
            --shadow-sm: 0 2px 8px rgba(31, 42, 41, 0.05);
            --shadow-card: 0 10px 28px rgba(53, 111, 107, 0.07);
            --shadow-soft: 0 14px 34px rgba(31, 42, 41, 0.06);
            --shadow-blue: 0 14px 28px rgba(76, 99, 244, 0.18);
            --shadow-teal: 0 12px 26px rgba(75, 149, 143, 0.12);
            --focus-blue: 0 0 0 4px rgba(76, 99, 244, 0.10);
            --focus-teal: 0 0 0 4px rgba(75, 149, 143, 0.12);
        }

        html, body {
            margin: 0;
            padding: 0;
            min-height: 100%;
            font-family: 'Inter', sans-serif;
        }

        body {
            min-height: 100vh;
            color: var(--text-primary);
            background:
                radial-gradient(circle at 50% 0%, rgba(110, 139, 255, 0.14), transparent 34%),
                radial-gradient(circle at 8% 84%, rgba(75, 149, 143, 0.09), transparent 30%),
                radial-gradient(circle at 90% 72%, rgba(215, 189, 98, 0.10), transparent 26%),
                linear-gradient(180deg, #F8FBFF 0%, #F7F9FD 48%, #F2F6F5 100%);
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
                linear-gradient(110deg, transparent 0%, transparent 44%, rgba(255,255,255,0.55) 46%, transparent 58%),
                radial-gradient(circle at 78% 12%, rgba(76, 99, 244, 0.055), transparent 22%);
            pointer-events: none;
            z-index: 0;
        }

        .modal-overlay {
            position: relative;
            z-index: 1;
            width: 100%;
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .modal-card {
            width: 100%;
            max-width: 980px;
            background: rgba(255, 255, 255, 0.84);
            border: 1px solid rgba(255, 255, 255, 0.88);
            border-radius: 34px;
            box-shadow: 0 26px 70px rgba(25, 35, 60, 0.08);
            overflow: hidden;
            display: grid;
            grid-template-columns: 0.86fr 1.38fr;
            min-height: calc(100vh - 4rem);
            backdrop-filter: blur(16px);
            position: relative;
            animation: createFadeUp 0.38s ease both;
        }

        .modal-card::before {
            content: '';
            position: absolute;
            inset: 0 auto 0 0;
            width: 7px;
            background: linear-gradient(180deg, rgba(215, 189, 98, 0.88), rgba(75, 149, 143, 0.86), rgba(76, 99, 244, 0.78));
            pointer-events: none;
            z-index: 2;
        }

        .borrow-side {
            position: relative;
            padding: 2.6rem 2.35rem;
            background:
                radial-gradient(circle at 8% 10%, rgba(215, 189, 98, 0.15), transparent 26%),
                linear-gradient(145deg, rgba(247, 243, 232, 0.98), rgba(231, 239, 237, 0.88));
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

        .borrow-side::before {
            content: '';
            position: absolute;
            width: 340px;
            height: 340px;
            right: -145px;
            top: -125px;
            border-radius: 0 0 0 100%;
            background: rgba(255, 255, 255, 0.52);
            pointer-events: none;
        }

        .borrow-side::after {
            content: 'BORROW';
            position: absolute;
            left: 2.25rem;
            bottom: 1.7rem;
            color: rgba(53, 111, 107, 0.06);
            font-size: clamp(3.2rem, 6vw, 4.8rem);
            line-height: 1;
            font-weight: 900;
            letter-spacing: 0.04em;
            pointer-events: none;
        }

        .side-content,
        .side-footer {
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
            font-size: 0.72rem;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 2rem;
            box-shadow: 0 8px 20px rgba(31, 42, 41, 0.05);
        }

        .system-dot {
            width: 9px;
            height: 9px;
            border-radius: 999px;
            background: #52C96C;
            box-shadow: 0 0 0 4px rgba(82, 201, 108, 0.10);
        }

        .side-title {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 900;
            letter-spacing: -0.065em;
            line-height: 1.05;
            color: var(--text-primary);
            margin: 0 0 1.05rem;
        }

        .side-title span {
            display: inline-block;
            color: var(--blue);
        }

        .side-copy {
            color: var(--text-muted);
            font-size: 0.96rem;
            font-weight: 700;
            line-height: 1.65;
            margin: 0;
        }

        .side-list {
            display: grid;
            gap: 0.9rem;
            list-style: none;
            margin: 2.25rem 0 0;
            padding: 0;
        }

        .side-list li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #3C475E;
            font-size: 0.9rem;
            font-weight: 800;
        }

        .side-list-icon {
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

        .side-footer {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            color: var(--text-soft);
            font-size: 0.8rem;
            font-weight: 800;
        }

        .side-footer-mark {
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

        .modal-main {
            display: flex;
            flex-direction: column;
            min-width: 0;
            background: rgba(255, 255, 255, 0.96);
            position: relative;
            overflow: hidden;
        }

        .modal-main::before {
            content: '';
            position: absolute;
            width: 280px;
            height: 280px;
            right: -82px;
            top: -98px;
            border-radius: 0 0 0 100%;
            background: linear-gradient(135deg, rgba(248,250,255,0.95), rgba(236,242,251,0.64));
            pointer-events: none;
        }

        .modal-body {
            position: relative;
            z-index: 1;
            padding: 2.45rem 2.65rem 1.4rem;
            overflow-y: auto;
            max-height: calc(100vh - 9rem);
        }

        .title-row {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.7rem;
        }

        .title-icon {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            background: linear-gradient(135deg, #5E78FF, var(--blue));
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            box-shadow: var(--shadow-blue);
            flex-shrink: 0;
        }

        .title-icon svg {
            width: 26px;
            height: 26px;
        }

        .modal-title {
            font-size: 1.85rem;
            font-weight: 900;
            color: var(--text-primary);
            margin: 0 0 0.35rem;
            letter-spacing: -0.055em;
            line-height: 1.1;
        }

        .modal-subtitle {
            margin: 0;
            color: var(--text-muted);
            font-size: 0.92rem;
            font-weight: 700;
            line-height: 1.55;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1.1rem;
        }

        .field {
            margin-bottom: 1.1rem;
        }

        .field.full {
            grid-column: 1 / -1;
        }

        .field > label,
        .field > .field-label {
            display: block;
            font-size: 0.76rem;
            font-weight: 900;
            color: #3C475E;
            margin-bottom: 0.5rem;
            letter-spacing: 0.07em;
            text-transform: uppercase;
        }

        .field-hint {
            font-size: 0.75rem;
            color: var(--text-soft);
            font-weight: 700;
            margin: 0.35rem 0 0;
            line-height: 1.45;
        }

        .field input[type="text"],
        .field input[type="number"],
        .field input[type="date"],
        .field select,
        .field textarea,
        .item-row input[type="number"],
        .item-row select {
            display: block;
            width: 100%;
            min-height: 50px;
            background: rgba(247, 243, 232, 0.72) !important;
            border: 1px solid rgba(99, 115, 146, 0.14) !important;
            border-radius: 16px !important;
            padding: 0.82rem 1rem !important;
            color: var(--text-primary) !important;
            font-size: 0.92rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 14px rgba(53, 111, 107, 0.04) !important;
            appearance: auto;
            -webkit-appearance: auto;
        }

        .field select,
        .item-row select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23657675' d='M1 1l5 5 5-5'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 1rem center !important;
            padding-right: 2.5rem !important;
            -webkit-appearance: none !important;
            appearance: none !important;
            cursor: pointer;
        }

        .field input::placeholder,
        .field textarea::placeholder,
        .item-row input::placeholder {
            color: var(--text-soft) !important;
            font-weight: 600;
        }

        .field input:focus,
        .field select:focus,
        .field textarea:focus,
        .item-row input:focus,
        .item-row select:focus {
            border-color: rgba(76, 99, 244, 0.34) !important;
            background: #FFFFFF !important;
            box-shadow: var(--focus-blue) !important;
            outline: none !important;
        }

        .field select option,
        .item-row select option {
            background: #FFFFFF;
            color: var(--text-primary);
        }

        .field textarea {
            resize: vertical;
            min-height: 118px;
            line-height: 1.6;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            opacity: 0.72;
            cursor: pointer;
        }

        .radio-group {
            display: flex;
            gap: 0.75rem;
            margin-top: 0.15rem;
            flex-wrap: wrap;
        }

        .radio-label {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            min-height: 42px;
            padding: 0.58rem 0.9rem;
            border-radius: 999px;
            background: rgba(231, 239, 237, 0.64);
            border: 1px solid rgba(75, 149, 143, 0.12);
            color: var(--text-muted);
            font-size: 0.88rem;
            font-weight: 900;
            cursor: pointer;
            transition: background 0.18s, color 0.18s, border-color 0.18s, transform 0.18s;
        }

        .radio-label:hover {
            transform: translateY(-1px);
            background: rgba(75, 149, 143, 0.11);
            color: var(--primary-dark);
        }

        .radio-label input[type="radio"] {
            accent-color: var(--blue);
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .items-box {
            padding: 1.2rem;
            border-radius: 22px;
            border: 1px solid rgba(75, 149, 143, 0.12);
            background: linear-gradient(180deg, rgba(250,251,250,0.94), rgba(247,243,232,0.68));
            box-shadow: 0 8px 22px rgba(31, 42, 41, 0.035);
        }

        .items-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 0.85rem;
        }

        .items-header-label {
            font-size: 0.76rem;
            font-weight: 900;
            color: #3C475E;
            letter-spacing: 0.07em;
            text-transform: uppercase;
        }

        .btn-add {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 34px;
            padding: 0.42rem 0.8rem;
            border-radius: 999px;
            border: 1px solid rgba(75, 149, 143, 0.16);
            background: rgba(75, 149, 143, 0.10);
            color: var(--primary-dark);
            font-size: 0.78rem;
            font-weight: 900;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            transition: background 0.18s, transform 0.18s, border-color 0.18s;
        }

        .btn-add:hover {
            background: rgba(75, 149, 143, 0.16);
            border-color: rgba(75, 149, 143, 0.24);
            transform: translateY(-1px);
        }

        .item-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 112px 38px;
            gap: 0.55rem;
            margin-bottom: 0.65rem;
            align-items: start;
        }

        .btn-remove {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 50px;
            border-radius: 16px;
            border: 1px solid rgba(201, 122, 98, 0.16);
            background: rgba(201, 122, 98, 0.08);
            color: var(--coral-dark);
            font-size: 1.45rem;
            line-height: 1;
            cursor: pointer;
            padding: 0;
            font-family: 'Inter', sans-serif;
            transition: background 0.18s, transform 0.18s, border-color 0.18s;
        }

        .btn-remove:hover {
            background: rgba(201, 122, 98, 0.15);
            border-color: rgba(201, 122, 98, 0.24);
            transform: translateY(-1px);
        }

        .modal-actions {
            position: relative;
            z-index: 1;
            display: flex;
            gap: 0.75rem;
            padding: 1rem 2.65rem 1.85rem;
            border-top: 1px solid rgba(75, 149, 143, 0.10);
            background: rgba(255, 255, 255, 0.94);
        }

        .btn-submit {
            flex: 1;
            min-height: 52px;
            padding: 0.9rem 1.2rem;
            background: linear-gradient(135deg, #5E78FF, var(--blue));
            border: none;
            border-radius: 16px;
            color: #fff;
            font-size: 0.95rem;
            font-weight: 900;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            transition: transform 0.18s, box-shadow 0.18s, filter 0.18s;
            letter-spacing: 0.01em;
            box-shadow: var(--shadow-blue);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            filter: saturate(0.98);
            box-shadow: 0 18px 34px rgba(76, 99, 244, 0.24);
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        .btn-batal {
            flex: 1;
            min-height: 52px;
            padding: 0.9rem 1.2rem;
            background: rgba(247, 243, 232, 0.78);
            border: 1px solid rgba(75, 149, 143, 0.14);
            border-radius: 16px;
            color: var(--text-muted);
            font-size: 0.95rem;
            font-weight: 900;
            text-align: center;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            transition: background 0.18s, color 0.18s, border-color 0.18s, transform 0.18s;
        }

        .btn-batal:hover {
            background: rgba(231, 239, 237, 0.88);
            color: var(--text-primary);
            border-color: rgba(75, 149, 143, 0.22);
            transform: translateY(-1px);
        }

        .modal-body::-webkit-scrollbar {
            width: 7px;
        }

        .modal-body::-webkit-scrollbar-track {
            background: transparent;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background: rgba(75, 149, 143, 0.16);
            border-radius: 999px;
        }

        .modal-body::-webkit-scrollbar-thumb:hover {
            background: rgba(75, 149, 143, 0.25);
        }

        [x-cloak] {
            display: none !important;
        }

        .combobox-wrap {
            position: relative;
        }

        .combobox-input {
            display: block;
            width: 100%;
            min-height: 50px;
            background: rgba(247, 243, 232, 0.72) !important;
            border: 1px solid rgba(99, 115, 146, 0.14) !important;
            border-radius: 16px !important;
            padding: 0.82rem 1rem !important;
            color: var(--text-primary) !important;
            font-size: 0.92rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
            cursor: text;
            box-shadow: 0 4px 14px rgba(53, 111, 107, 0.04) !important;
        }

        .combobox-input:focus {
            border-color: rgba(76, 99, 244, 0.34) !important;
            background: #FFFFFF !important;
            box-shadow: var(--focus-blue) !important;
        }

        .combobox-input::placeholder {
            color: var(--text-soft) !important;
            font-weight: 600;
        }

        .combobox-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            background: #FAFBFA;
            border: 1px solid rgba(75, 149, 143, 0.16);
            border-radius: 18px;
            max-height: 230px;
            overflow-y: auto;
            z-index: 100;
            box-shadow: 0 18px 42px rgba(31, 42, 41, 0.14);
            padding: 0.35rem;
        }

        .combobox-option {
            padding: 0.65rem 0.75rem;
            font-size: 0.86rem;
            color: var(--text-muted);
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.75rem;
            border-radius: 13px;
            font-weight: 800;
        }

        .combobox-option:hover,
        .combobox-option.is-active {
            background: rgba(231, 239, 237, 0.88);
            color: var(--primary-dark);
        }

        .combobox-option .stock-badge {
            font-size: 0.72rem;
            color: var(--primary-dark);
            background: rgba(75, 149, 143, 0.10);
            border: 1px solid rgba(75, 149, 143, 0.12);
            padding: 0.15rem 0.5rem;
            border-radius: 999px;
            white-space: nowrap;
            font-weight: 900;
        }

        .combobox-empty {
            padding: 0.85rem 1rem;
            font-size: 0.85rem;
            color: var(--text-soft);
            text-align: center;
            font-weight: 800;
        }

        .combobox-dropdown::-webkit-scrollbar {
            width: 5px;
        }

        .combobox-dropdown::-webkit-scrollbar-track {
            background: transparent;
        }

        .combobox-dropdown::-webkit-scrollbar-thumb {
            background: rgba(75, 149, 143, 0.18);
            border-radius: 999px;
        }

        @keyframes createFadeUp {
            from {
                opacity: 0;
                transform: translateY(14px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 920px) {
            .modal-overlay {
                padding: 1.25rem;
            }

            .modal-card {
                grid-template-columns: 1fr;
                min-height: auto;
                border-radius: 30px;
            }

            .borrow-side {
                min-height: auto;
                padding: 2.1rem 2rem;
            }

            .borrow-side::after,
            .side-list,
            .side-footer {
                display: none;
            }

            .modal-body {
                max-height: none;
                overflow: visible;
                padding: 2.15rem 2rem 1.3rem;
            }

            .modal-actions {
                padding: 1rem 2rem 1.7rem;
            }
        }

        @media (max-width: 680px) {
            .modal-overlay {
                padding: 0;
                align-items: stretch;
            }

            .modal-card {
                max-width: 100%;
                border-radius: 0;
                min-height: 100vh;
            }

            .borrow-side {
                padding: 1.6rem 1.25rem;
            }

            .modal-body {
                padding: 1.45rem 1.25rem 1rem;
            }

            .title-row {
                gap: 0.85rem;
            }

            .title-icon {
                width: 50px;
                height: 50px;
                border-radius: 16px;
            }

            .modal-title {
                font-size: 1.55rem;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .item-row {
                grid-template-columns: 1fr;
                padding: 0.85rem;
                border-radius: 18px;
                background: rgba(255, 255, 255, 0.64);
                border: 1px solid rgba(75, 149, 143, 0.10);
            }

            .btn-remove {
                width: 100%;
                height: 42px;
            }

            .items-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .btn-add {
                width: 100%;
            }

            .modal-actions {
                flex-direction: column;
                padding: 0.9rem 1.25rem 1.35rem;
            }

            .btn-submit,
            .btn-batal {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <div class="modal-overlay" x-data="borrowingForm()">

        <div class="modal-card">

            <aside class="borrow-side">
                <div class="side-content">
                    <div class="system-badge">
                        <span class="system-dot"></span>
                        Borrowing Request
                    </div>

                    <h1 class="side-title">
                        Ajukan<br><span>Peminjaman Baru</span>
                    </h1>
                    <p class="side-copy">
                        Lengkapi detail ruangan, waktu, keperluan, dan alat yang ingin dipinjam agar pengajuan dapat diproses admin.
                    </p>

                    <ul class="side-list">
                        <li>
                            <span class="side-list-icon">1</span>
                            Pilih tanggal, ruangan, dan slot waktu tersedia
                        </li>
                        <li>
                            <span class="side-list-icon">2</span>
                            Isi keperluan dan jumlah pengguna ruangan
                        </li>
                        <li>
                            <span class="side-list-icon">3</span>
                            Tambahkan alat beserta jumlah yang dibutuhkan
                        </li>
                    </ul>
                </div>

                <div class="side-footer">
                    <span class="side-footer-mark">
                        ✓
                    </span>
                    Laboratory Management System
                </div>
            </aside>

            <div class="modal-main">
                <div class="modal-body">

                    <div class="title-row">
                        <span class="title-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M8 6h13"/>
                                <path d="M8 12h13"/>
                                <path d="M8 18h13"/>
                                <path d="M3 6h.01"/>
                                <path d="M3 12h.01"/>
                                <path d="M3 18h.01"/>
                            </svg>
                        </span>
                        <div>
                            <p class="modal-title">Peminjaman Baru</p>
                            <p class="modal-subtitle">Isi formulir peminjaman laboratorium dengan data yang benar.</p>
                        </div>
                    </div>

                    <x-alert-error />

                    <form method="POST" action="{{ route('student.borrowings.store') }}" id="theForm">
                        @csrf

                        <div class="form-grid">
                            <div class="field">
                                <label>Tanggal Peminjaman</label>
                                <input type="date" name="borrow_date" x-model="borrow_date" @change="loadSchedules" required>
                            </div>

                            <div class="field">
                                <label>Ruangan</label>
                                <select name="room_id" x-model="room_id" @change="loadSchedules" required>
                                    <option value="">Pilih Ruangan</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}">{{ $room->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="field full">
                                <label>Waktu</label>
                                <select name="time_slot" x-model="time_slot" required>
                                    <option value="">Pilih Waktu</option>
                                    <template x-for="slot in schedules">
                                        <option :value="slot.time_slot" x-text="slot.time_slot"></option>
                                    </template>
                                </select>
                                <p class="field-hint">Pilih ruangan dan tanggal terlebih dahulu</p>
                                <p class="field-hint"
                                    x-show="scheduleMessage"
                                    x-text="scheduleMessage">
                                </p>
                            </div>

                            <div class="field full">
                                <label>Keperluan</label>
                                <textarea name="purpose" rows="4" required placeholder="Jelaskan keperluan peminjaman"></textarea>
                            </div>

                            <div class="field">
                                <label>Jumlah Orang</label>
                                <input type="number" name="total_people" required min="1"
                                       placeholder="Masukkan jumlah orang yang akan menggunakan">
                            </div>

                            <div class="field">
                                <label>Dibersamai Dosen?</label>
                                <div class="radio-group">
                                    <label class="radio-label">
                                        <input type="radio" name="with_lecturer" value="1" x-model="with_lecturer"> Ya
                                    </label>
                                    <label class="radio-label">
                                        <input type="radio" name="with_lecturer" value="0" x-model="with_lecturer"> Tidak
                                    </label>
                                </div>
                            </div>

                            <div class="field full" x-show="with_lecturer == 1" x-transition>
                                <label>Nama Dosen</label>
                                <input type="text" name="lecturer_name" placeholder="Masukkan nama dosen">
                            </div>

                            <!-- ── item-row tetap pakai combobox, bukan <select> ── -->
                            <div class="field full">
                                <div class="items-box">
                                    <div class="items-header">
                                        <span class="items-header-label">Alat yang Dipinjam</span>
                                        <button type="button" @click="addItem" class="btn-add">+ Tambah Alat</button>
                                    </div>

                                    <template x-for="(item, index) in items" :key="index">
                                        <div class="item-row" x-data="combobox(index, items)">

                                            <!-- hidden input untuk submit -->
                                            <input type="hidden" :name="'items['+index+'][item_id]'" :value="items[index].item_id">

                                            <!-- searchable combobox -->
                                            <div class="combobox-wrap">
                                                <input
                                                    type="text"
                                                    class="combobox-input"
                                                    placeholder="Cari alat..."
                                                    x-model="search"
                                                    @focus="open = true"
                                                    @input="open = true"
                                                    @keydown.escape="open = false"
                                                    @keydown.arrow-down.prevent="moveDown"
                                                    @keydown.arrow-up.prevent="moveUp"
                                                    @keydown.enter.prevent="selectHighlighted(index)"
                                                    @blur="handleBlur"
                                                    autocomplete="off"
                                                >

                                                <div class="combobox-dropdown" x-show="open" x-cloak>
                                                    <template x-if="filtered.length === 0">
                                                        <div class="combobox-empty">Alat tidak ditemukan</div>
                                                    </template>
                                                    <template x-for="(opt, i) in filtered" :key="opt.id">
                                                        <div
                                                            class="combobox-option"
                                                            :class="{ 'is-active': highlighted === i }"
                                                            @mousedown.prevent="selectOption(opt, index)"
                                                        >
                                                            <span x-text="opt.name"></span>
                                                            <span class="stock-badge">stok: <span x-text="opt.stock"></span></span>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>

                                            <div>
                                                <input
                                                    type="number" min="1"
                                                    :max="items[index].max_stock || 1"
                                                    @change="validateQty(index)"
                                                    x-model="items[index].qty"
                                                    placeholder="Qty"
                                                    :name="'items['+index+'][qty]'"
                                                >
                                                <p class="field-hint" x-show="items[index].max_stock">
                                                    Maks: <span x-text="items[index].max_stock"></span>
                                                </p>
                                            </div>

                                            <button type="button" @click="removeItem(index)" class="btn-remove">×</button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

                <div class="modal-actions">
                    <button type="submit" form="theForm" class="btn-submit">Submit</button>
                    <a href="{{ route('student.dashboard') }}" class="btn-batal">Batal</a>
                </div>
            </div>

        </div>
    </div>

    <!-- expose $items sebagai JSON untuk Alpine combobox -->
    <script>
        const ALL_ITEMS = @json($items->map(fn($i) => ['id' => $i->id, 'name' => $i->name, 'stock' => $i->stock]));
    </script>

    <script>
    // borrowingForm() — items punya item_id
    function borrowingForm() {
        return {
            borrow_date: '',
            room_id: '',
            schedules: [],
            time_slot: '',
            scheduleMessage: '',
            with_lecturer: 0,
            items: [{ item_id: '', qty: '', max_stock: 0 }],

            addItem()         { this.items.push({ item_id: '', qty: '', max_stock: 0 }); },
            removeItem(index) { this.items.splice(index, 1); },

            async loadSchedules() {
                this.time_slot = '';
                this.schedules = [];
                this.scheduleMessage = '';

                if (!this.borrow_date || !this.room_id) return;

                const res = await fetch(
                    `/student/available-schedules?room_id=${this.room_id}&date=${this.borrow_date}`
                );

                const data = await res.json();

                if (data.message) {
                    this.scheduleMessage = data.message;
                    return;
                }

                this.schedules = data;
            },

            validateQty(index) {
                const qty = parseInt(this.items[index].qty);
                const max = this.items[index].max_stock;
                if (qty > max) {
                    this.items[index].qty = max;
                    alert('Stok maksimal saat ini: ' + max);
                }
            }
        }
    }

    // fungsi combobox()
    function combobox(index, items) {
        return {
            search: '',
            open: false,
            highlighted: 0,

            get filtered() {
                if (!this.search.trim()) return ALL_ITEMS;
                const q = this.search.toLowerCase();
                return ALL_ITEMS.filter(o => o.name.toLowerCase().includes(q));
            },

            selectOption(opt, index) {
                items[index].item_id   = opt.id;
                items[index].max_stock = opt.stock;
                this.search      = opt.name;
                this.open        = false;
                this.highlighted = 0;
            },

            selectHighlighted(index) {
                if (this.filtered[this.highlighted]) {
                    this.selectOption(this.filtered[this.highlighted], index);
                }
            },

            moveDown() {
                this.highlighted = this.highlighted < this.filtered.length - 1
                    ? this.highlighted + 1
                    : 0;
            },

            moveUp() {
                this.highlighted = this.highlighted > 0
                    ? this.highlighted - 1
                    : this.filtered.length - 1;
            },

            handleBlur() {
                // delay agar mousedown pada option sempat terpanggil dulu
                setTimeout(() => { this.open = false; }, 150);
            }
        }
    }
    </script>

</body>
</html>
