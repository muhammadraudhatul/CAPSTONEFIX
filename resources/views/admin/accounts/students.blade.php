@extends('admin.layouts.app')

@push('styles')
<style>
    .dash-wrapper { --bg-card: #161b27; --border: #252d3d; --text-primary: #e8edf5; --text-muted: #6b7a99; font-family: 'Inter', sans-serif; color: var(--text-primary); padding: 1.5rem; }
    .card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; padding: 1.5rem; }
    table { width: 100%; border-collapse: collapse; }
    th { color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; padding: 1rem; text-align: left; }
    td { padding: 1rem; border-top: 1px solid var(--border); }
</style>
@endpush

@section('content')
<div class="dash-wrapper">
    <div class="mb-8">
        <h1 style="font-size: 1.75rem; font-weight: 800;">Kelola Akun Student</h1>
        <p style="color: var(--text-muted);">Daftar seluruh pengguna mahasiswa dalam sistem.</p>
    </div>

    <div class="card">
        <table>
            <thead><tr><th>Nama</th><th>NIM</th><th>Registrasi</th><th class="text-center">Aksi</th></tr></thead>
            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td class="font-medium">{{ $student->name }}</td>
                        <td>{{ $student->nim }}</td>
                        <td>{{ $student->created_at->format('d M Y') }}</td>
                        <td class="text-center">
                            <form action="{{ route('admin.accounts.students.destroy',$student) }}" method="POST">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('Hapus student?')" class="text-red-400 hover:text-red-300 font-bold text-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center py-10 text-muted">Data tidak tersedia.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection