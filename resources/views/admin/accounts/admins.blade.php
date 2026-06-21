@extends('admin.layouts.app')

@push('styles')
<style>
    .accounts-flip7-wrapper {
        --primary-teal:  #4B958F;
        --primary-light: #75B6B0;
        --primary-dark:  #356F6B;
        --primary-bg:    #E7EFED;
        --accent-gold:   #D7BD62;
        --accent-light:  #E9DCA7;
        --coral:         #C97A62;
        --coral-dark:    #A85F49;
        --cream:         #F7F3E8;
        --surface-base:  #EDF2F1;
        --surface-card:  #FAFBFA;
        --text-primary:  #1F2A29;
        --text-muted:    #657675;
        --text-soft:     #8A9997;
        --border-soft:   rgba(53, 111, 107, 0.13);
        --shadow-sm:     0 2px 8px rgba(31, 42, 41, 0.05);
        --shadow-card:   0 10px 28px rgba(53, 111, 107, 0.07);
        --shadow-soft:   0 14px 34px rgba(31, 42, 41, 0.06);

        min-height: calc(100vh - 1px);
        padding: 2rem 1.75rem;
        border-radius: 28px;
        position: relative;
        overflow: hidden;
        color: var(--text-primary);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'PingFang SC', 'Microsoft YaHei', sans-serif;
        background:
            radial-gradient(circle at 8% 8%, rgba(215, 189, 98, 0.09), transparent 25%),
            radial-gradient(circle at 88% 12%, rgba(75, 149, 143, 0.08), transparent 28%),
            linear-gradient(135deg, #F3F6F5 0%, var(--surface-base) 52%, #EEF3F1 100%);
    }

    .accounts-flip7-wrapper *,
    .accounts-flip7-wrapper *::before,
    .accounts-flip7-wrapper *::after {
        box-sizing: border-box;
    }

    .accounts-flip7-wrapper::before,
    .accounts-flip7-wrapper::after {
        content: '';
        position: absolute;
        border-radius: 28px;
        border: 1px solid rgba(53, 111, 107, 0.05);
        background: rgba(247, 243, 232, 0.20);
        pointer-events: none;
        z-index: 0;
    }

    .accounts-flip7-wrapper::before {
        width: 150px;
        height: 210px;
        right: -74px;
        top: 110px;
        transform: rotate(-8deg);
    }

    .accounts-flip7-wrapper::after {
        width: 108px;
        height: 146px;
        left: -52px;
        bottom: 120px;
        transform: rotate(10deg);
    }

    .accounts-flip7-main,
    .accounts-header,
    .account-card,
    .success-card {
        position: relative;
        z-index: 1;
    }

    .accounts-header {
        padding: 1.65rem 1.8rem;
        border-radius: 30px;
        background: linear-gradient(135deg, rgba(247, 243, 232, 0.96), rgba(250, 251, 250, 0.95));
        border: 1px solid rgba(255, 255, 255, 0.80);
        box-shadow: var(--shadow-card);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .accounts-header::before {
        content: '';
        position: absolute;
        inset: 0 auto 0 0;
        width: 6px;
        background: linear-gradient(180deg, rgba(215, 189, 98, 0.85), rgba(75, 149, 143, 0.85), rgba(201, 122, 98, 0.78));
    }

    .accounts-header::after {
        content: 'ADMIN ACCOUNTS';
        position: absolute;
        right: 1.5rem;
        bottom: 0.85rem;
        color: rgba(53, 111, 107, 0.05);
        font-weight: 900;
        font-size: clamp(1.7rem, 5vw, 3.8rem);
        line-height: 1;
        letter-spacing: 0.08em;
        pointer-events: none;
    }

    .accounts-title-block {
        position: relative;
        z-index: 1;
    }

    .accounts-title {
        font-size: clamp(1.9rem, 3vw, 2.55rem);
        font-weight: 900;
        color: var(--text-primary);
        letter-spacing: -0.045em;
        margin: 0 0 0.45rem;
        line-height: 1.05;
        text-shadow: 2px 2px 0 rgba(215, 189, 98, 0.20);
    }

    .accounts-subtitle {
        color: #4C8A85;
        font-size: 0.95rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        margin: 0;
    }

    .success-card {
        color: #3C7359;
        background: linear-gradient(135deg, rgba(79,142,114,0.13), rgba(250,251,250,0.94));
        border: 1px solid rgba(79,142,114,0.20);
        border-left: 7px solid #4F8E72;
        border-radius: 22px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.25rem;
        font-size: 0.9rem;
        font-weight: 900;
        box-shadow: var(--shadow-sm);
    }

    .accounts-layout {
        display: grid;
        grid-template-columns: minmax(280px, 0.85fr) minmax(0, 2fr);
        gap: 1.5rem;
        position: relative;
        z-index: 1;
    }

    .account-card {
        background: linear-gradient(180deg, rgba(250,251,250,0.98), rgba(244, 241, 234, 0.88));
        border: 1px solid rgba(255, 255, 255, 0.82);
        border-left: 7px solid var(--primary-teal);
        border-radius: 26px;
        padding: 1.5rem;
        box-shadow: var(--shadow-soft);
        overflow: hidden;
        transition: transform 0.24s cubic-bezier(.2,.8,.2,1), box-shadow 0.24s, border-color 0.24s;
    }

    .account-card:hover {
        transform: translateY(-2px);
        border-color: rgba(75, 149, 143, 0.12);
        box-shadow: 0 16px 32px rgba(31, 42, 41, 0.07);
    }

    .account-card-title {
        color: var(--text-primary);
        font-size: 1.05rem;
        font-weight: 900;
        letter-spacing: -0.02em;
        margin: 0 0 1.25rem;
    }

    .field-group {
        margin-bottom: 1rem;
    }

    .field-label {
        display: block;
        margin-bottom: 0.5rem;
        color: var(--text-muted);
        font-size: 0.72rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.10em;
    }

    .field-input {
        width: 100%;
        border-radius: 16px;
        padding: 0.85rem 1rem;
        background: rgba(247, 243, 232, 0.88) !important;
        border: 1px solid rgba(75, 149, 143, 0.16) !important;
        color: var(--text-primary) !important;
        font-size: 0.9rem;
        font-weight: 700;
        outline: none;
        box-shadow: 0 4px 14px rgba(53, 111, 107, 0.04) !important;
        transition: border-color 0.18s, background 0.18s, box-shadow 0.18s;
    }

    .field-input:focus {
        background: #FAFBFA !important;
        border-color: rgba(75, 149, 143, 0.38) !important;
        box-shadow: 0 0 0 4px rgba(75, 149, 143, 0.10) !important;
    }

    .btn-save {
        width: 100%;
        min-height: 46px;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        padding: 0.78rem 1.35rem;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.75);
        color: #43340E;
        background: linear-gradient(135deg, #E6D58C, #D7BD62);
        font-size: 0.9rem;
        font-weight: 900;
        cursor: pointer;
        box-shadow: 0 8px 20px rgba(31, 42, 41, 0.05);
        transition: transform 0.18s, filter 0.18s, box-shadow 0.18s;
    }

    .btn-save:hover {
        transform: translateY(-2px);
        filter: saturate(0.96);
        box-shadow: 0 10px 22px rgba(53, 111, 107, 0.08);
    }

    .table-card {
        padding: 0;
        overflow-x: auto;
    }

    .accounts-table {
        width: 100%;
        min-width: 640px;
        border-collapse: collapse;
        text-align: left;
    }

    .accounts-table thead tr {
        background: rgba(231, 239, 237, 0.88);
    }

    .accounts-table th {
        padding: 0.92rem 1rem;
        color: #4A8C86;
        font-size: 0.72rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.10em;
        border-bottom: 2px solid rgba(75, 149, 143, 0.10);
        white-space: nowrap;
    }

    .accounts-table td {
        padding: 1rem;
        color: var(--text-muted);
        font-size: 0.9rem;
        font-weight: 700;
        border-top: 1px solid rgba(75, 149, 143, 0.08);
        vertical-align: middle;
    }

    .accounts-table tbody tr:hover {
        background: rgba(247, 243, 232, 0.58);
    }

    .td-name {
        color: var(--text-primary);
        font-weight: 900;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 28px;
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
        font-size: 0.68rem;
        font-weight: 900;
        letter-spacing: 0.06em;
        white-space: nowrap;
        border: 1px solid rgba(255, 255, 255, 0.60);
        box-shadow: var(--shadow-sm);
    }

    .status-main {
        background: linear-gradient(135deg, rgba(215,189,98,0.20), rgba(215,189,98,0.10));
        color: #7F6B2A;
        border-color: rgba(215,189,98,0.22);
    }

    .status-admin {
        background: linear-gradient(135deg, rgba(75,149,143,0.16), rgba(75,149,143,0.08));
        color: var(--primary-dark);
        border-color: rgba(75,149,143,0.18);
    }

    .action-muted {
        color: var(--text-soft);
        font-size: 0.75rem;
        font-weight: 800;
    }

    .btn-delete {
        border: none;
        background: none;
        color: var(--coral-dark);
        font-size: 0.82rem;
        font-weight: 900;
        cursor: pointer;
        transition: color 0.18s, transform 0.18s;
    }

    .btn-delete:hover {
        color: #8F4737;
        transform: translateY(-1px);
    }

    @keyframes accountFadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .accounts-header,
    .success-card,
    .account-card {
        animation: accountFadeUp 0.35s ease both;
    }

    .accounts-layout .account-card:nth-child(1) { animation-delay: 0.08s; }
    .accounts-layout .account-card:nth-child(2) { animation-delay: 0.12s; }

    @media (max-width: 980px) {
        .accounts-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .accounts-flip7-wrapper {
            padding: 1.25rem 1rem;
            border-radius: 22px;
        }

        .accounts-header {
            padding: 1.35rem 1.25rem;
            border-radius: 24px;
        }
    }
</style>
@endpush

@section('content')
<div class="accounts-flip7-wrapper">
    <div class="accounts-flip7-main">

        <div class="accounts-header">
            <div class="accounts-title-block">
                <h1 class="accounts-title">Kelola Akun Admin</h1>
                <p class="accounts-subtitle">Manajemen akses kontrol untuk administrator sistem.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="success-card">{{ session('success') }}</div>
        @endif

        <div class="accounts-layout">
            <div class="account-card">
                <h2 class="account-card-title">Tambah Admin</h2>

                <form action="{{ route('admin.accounts.admins.store') }}" method="POST">
                    @csrf

                    <div class="field-group">
                        <label class="field-label">Nama</label>
                        <input type="text" name="name" class="field-input">
                    </div>

                    <div class="field-group">
                        <label class="field-label">Username</label>
                        <input type="text" name="nim" class="field-input">
                    </div>

                    <div class="field-group">
                        <label class="field-label">Password</label>
                        <input type="password" name="password" class="field-input">
                    </div>

                    <div class="field-group" style="margin-bottom: 1.5rem;">
                        <label class="field-label">Konfirmasi</label>
                        <input type="password" name="password_confirmation" class="field-input">
                    </div>

                    <button type="submit" class="btn-save">Simpan Admin</button>
                </form>
            </div>

            <div class="account-card table-card">
                <table class="accounts-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Status</th>
                            <th style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($admins as $admin)
                            <tr>
                                <td class="td-name">{{ $admin->name }}</td>
                                <td>{{ $admin->nim }}</td>
                                <td>
                                    <span class="status-badge {{ $admin->nim === 'admin' ? 'status-main' : 'status-admin' }}">
                                        {{ $admin->nim === 'admin' ? 'Admin Utama' : 'Admin' }}
                                    </span>
                                </td>
                                <td style="text-align:center;">
                                {{-- Hanya muncul jika: 
                                    1. Bukan akun sendiri 
                                    2. Bukan akun dengan tag 'admin' (Admin Utama) 
                                --}}
                                @if($admin->id !== auth()->id() && $admin->nim !== 'admin')
                                    <form action="{{ route('admin.accounts.admins.destroy', $admin) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button onclick="return confirm('Hapus admin ini?')" class="btn-delete">
                                            Hapus
                                        </button>
                                    </form>
                                @elseif($admin->nim === 'admin')
                                    <span class="action-muted" style="font-style:italic;">System</span>
                                @else
                                    <span class="action-muted">Aktif</span>
                                @endif
                            </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
