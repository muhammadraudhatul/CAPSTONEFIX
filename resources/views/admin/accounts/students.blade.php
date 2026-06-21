@extends('admin.layouts.app')

@push('styles')
<style>
    .students-flip7-wrapper {
        --primary-teal:  #4B958F;
        --primary-light: #75B6B0;
        --primary-dark:  #356F6B;
        --accent-gold:   #D7BD62;
        --coral:         #C97A62;
        --coral-dark:    #A85F49;
        --cream:         #F7F3E8;
        --surface-base:  #EDF2F1;
        --surface-card:  #FAFBFA;
        --text-primary:  #1F2A29;
        --text-muted:    #657675;
        --text-soft:     #8A9997;
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

    .students-flip7-wrapper *,
    .students-flip7-wrapper *::before,
    .students-flip7-wrapper *::after {
        box-sizing: border-box;
    }

    .students-flip7-wrapper::before,
    .students-flip7-wrapper::after {
        content: '';
        position: absolute;
        border-radius: 28px;
        border: 1px solid rgba(53, 111, 107, 0.05);
        background: rgba(247, 243, 232, 0.20);
        pointer-events: none;
        z-index: 0;
    }

    .students-flip7-wrapper::before {
        width: 150px;
        height: 210px;
        right: -74px;
        top: 110px;
        transform: rotate(-8deg);
    }

    .students-flip7-wrapper::after {
        width: 108px;
        height: 146px;
        left: -52px;
        bottom: 120px;
        transform: rotate(10deg);
    }

    .students-flip7-main,
    .students-header,
    .students-card {
        position: relative;
        z-index: 1;
    }

    .students-header {
        padding: 1.65rem 1.8rem;
        border-radius: 30px;
        background: linear-gradient(135deg, rgba(247, 243, 232, 0.96), rgba(250, 251, 250, 0.95));
        border: 1px solid rgba(255, 255, 255, 0.80);
        box-shadow: var(--shadow-card);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .students-header::before {
        content: '';
        position: absolute;
        inset: 0 auto 0 0;
        width: 6px;
        background: linear-gradient(180deg, rgba(215, 189, 98, 0.85), rgba(75, 149, 143, 0.85), rgba(201, 122, 98, 0.78));
    }

    .students-header::after {
        content: 'STUDENT ACCOUNTS';
        position: absolute;
        right: 1.5rem;
        bottom: 0.85rem;
        color: rgba(53, 111, 107, 0.05);
        font-weight: 900;
        font-size: clamp(1.6rem, 5vw, 3.7rem);
        line-height: 1;
        letter-spacing: 0.08em;
        pointer-events: none;
    }

    .students-title-block {
        position: relative;
        z-index: 1;
    }

    .students-title {
        font-size: clamp(1.9rem, 3vw, 2.55rem);
        font-weight: 900;
        color: var(--text-primary);
        letter-spacing: -0.045em;
        margin: 0 0 0.45rem;
        line-height: 1.05;
        text-shadow: 2px 2px 0 rgba(215, 189, 98, 0.20);
    }

    .students-subtitle {
        color: #4C8A85;
        font-size: 0.95rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        margin: 0;
    }

    .students-card {
        background: linear-gradient(180deg, rgba(250,251,250,0.98), rgba(244, 241, 234, 0.88));
        border: 1px solid rgba(255, 255, 255, 0.82);
        border-left: 7px solid var(--primary-teal);
        border-radius: 26px;
        padding: 0;
        box-shadow: var(--shadow-soft);
        overflow-x: auto;
        transition: transform 0.24s cubic-bezier(.2,.8,.2,1), box-shadow 0.24s, border-color 0.24s;
    }

    .students-card:hover {
        transform: translateY(-2px);
        border-color: rgba(75, 149, 143, 0.12);
        box-shadow: 0 16px 32px rgba(31, 42, 41, 0.07);
    }

    .students-table {
        width: 100%;
        min-width: 640px;
        border-collapse: collapse;
        text-align: left;
    }

    .students-table thead tr {
        background: rgba(231, 239, 237, 0.88);
    }

    .students-table th {
        padding: 0.92rem 1rem;
        color: #4A8C86;
        font-size: 0.72rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.10em;
        border-bottom: 2px solid rgba(75, 149, 143, 0.10);
        white-space: nowrap;
        text-align: left;
    }

    .students-table th.text-center {
        text-align: center;
    }

    .students-table td {
        padding: 1rem;
        color: var(--text-muted);
        font-size: 0.9rem;
        font-weight: 700;
        border-top: 1px solid rgba(75, 149, 143, 0.08);
        vertical-align: middle;
    }

    .students-table tbody tr:hover {
        background: rgba(247, 243, 232, 0.58);
    }

    .students-table .font-medium {
        color: var(--text-primary);
        font-weight: 900;
    }

    .students-table .text-center {
        text-align: center;
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

    .empty-cell {
        text-align: center;
        padding: 3rem 1rem !important;
        color: var(--text-soft) !important;
        font-weight: 900;
        background: rgba(247, 243, 232, 0.46);
    }

    @keyframes studentFadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .students-header,
    .students-card {
        animation: studentFadeUp 0.35s ease both;
    }

    .students-card {
        animation-delay: 0.08s;
    }

    @media (max-width: 640px) {
        .students-flip7-wrapper {
            padding: 1.25rem 1rem;
            border-radius: 22px;
        }

        .students-header {
            padding: 1.35rem 1.25rem;
            border-radius: 24px;
        }
    }
</style>
@endpush

@section('content')
<div class="students-flip7-wrapper">
    <div class="students-flip7-main">

        <div class="students-header">
            <div class="students-title-block">
                <h1 class="students-title">Kelola Akun Student</h1>
                <p class="students-subtitle">Daftar seluruh pengguna mahasiswa dalam sistem.</p>
            </div>
        </div>

        <div class="students-card">
            <table class="students-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>NIM</th>
                        <th>Registrasi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td class="font-medium">{{ $student->name }}</td>
                            <td>{{ $student->nim }}</td>
                            <td>{{ $student->created_at->format('d M Y') }}</td>
                            <td class="text-center">
                                <form action="{{ route('admin.accounts.students.destroy',$student) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button onclick="return confirm('Hapus student?')" class="btn-delete">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="empty-cell">Data tidak tersedia.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection
