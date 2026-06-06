@extends('admin.layouts.app')

@push('styles')
<style>
    .dash-wrapper { --bg-card: #161b27; --border: #252d3d; --text-primary: #e8edf5; --text-muted: #6b7a99; font-family: 'Inter', sans-serif; color: var(--text-primary); padding: 1.5rem; }
    .card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; padding: 1.5rem; }
    input { background: #0d1117 !important; border: 1px solid var(--border) !important; color: white !important; }
    table { color: var(--text-primary); }
    thead { border-bottom: 1px solid var(--border); }
</style>
@endpush

@section('content')
<div class="dash-wrapper">
    <div class="mb-8">
        <h1 style="font-size: 1.75rem; font-weight: 800;">Kelola Akun Admin</h1>
        <p style="color: var(--text-muted);">Manajemen akses kontrol untuk administrator sistem.</p>
    </div>

    @if(session('success'))
        <div class="card mb-6" style="color: #22c55e; border-color: #22c55e;">{{ session('success') }}</div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
        <div class="card">
            <h2 style="font-weight: 700; margin-bottom: 1.5rem;">Tambah Admin</h2>
            <form action="{{ route('admin.accounts.admins.store') }}" method="POST">
                @csrf
                <div style="margin-bottom: 1rem;"><label class="block mb-2 text-xs uppercase text-muted">Nama</label><input type="text" name="name" class="w-full rounded-lg p-3"></div>
                <div style="margin-bottom: 1rem;"><label class="block mb-2 text-xs uppercase text-muted">Username</label><input type="text" name="nim" class="w-full rounded-lg p-3"></div>
                <div style="margin-bottom: 1rem;"><label class="block mb-2 text-xs uppercase text-muted">Password</label><input type="password" name="password" class="w-full rounded-lg p-3"></div>
                <div style="margin-bottom: 1.5rem;"><label class="block mb-2 text-xs uppercase text-muted">Konfirmasi</label><input type="password" name="password_confirmation" class="w-full rounded-lg p-3"></div>
                <button type="submit" class="w-full bg-[#4f7cff] text-white py-3 rounded-xl font-bold hover:bg-blue-600 transition">Simpan Admin</button>
            </form>
        </div>

        <div class="card overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr style="color: var(--text-muted); font-size: 0.8rem;">
                        <th class="p-4">Nama</th>
                        <th class="p-4">Username</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($admins as $admin)
                        <tr style="border-top: 1px solid var(--border);">
                            <td class="p-4 font-medium">{{ $admin->name }}</td>
                            <td class="p-4">{{ $admin->nim }}</td>
                            <td class="p-4">
                                <span class="px-3 py-1 rounded-full text-xs {{ $admin->nim === 'admin' ? 'bg-[#8b5cf6]/20 text-[#8b5cf6]' : 'bg-[#22c55e]/20 text-[#22c55e]' }}">
                                    {{ $admin->nim === 'admin' ? 'Admin Utama' : 'Admin' }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                            {{-- Hanya muncul jika: 
                                1. Bukan akun sendiri 
                                2. Bukan akun dengan tag 'admin' (Admin Utama) 
                            --}}
                            @if($admin->id !== auth()->id() && $admin->nim !== 'admin')
                                <form action="{{ route('admin.accounts.admins.destroy', $admin) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button onclick="return confirm('Hapus admin ini?')" class="text-red-400 hover:text-red-300 font-bold text-sm">
                                        Hapus
                                    </button>
                                </form>
                            @elseif($admin->nim === 'admin')
                                <span class="text-muted text-xs italic">System</span>
                            @else
                                <span class="text-muted text-xs">Aktif</span>
                            @endif
                        </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection