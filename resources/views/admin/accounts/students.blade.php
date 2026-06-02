@extends('admin.layouts.app')

@section('content')

<div class="mb-8">

    <h1 class="text-3xl font-bold text-gray-800">
        Kelola Akun Student
    </h1>

    <p class="text-gray-500 mt-2">
        Daftar seluruh akun student yang terdaftar pada sistem.
    </p>

</div>

@if(session('success'))

    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
        {{ session('success') }}
    </div>

@endif

<div class="bg-white rounded-3xl shadow overflow-hidden">

    <table class="w-full">

        <thead class="bg-gray-50">

            <tr>

                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">
                    Nama
                </th>

                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">
                    NIM
                </th>

                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">
                    Tanggal Registrasi
                </th>

                <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">
                    Aksi
                </th>

            </tr>

        </thead>

        <tbody>

            @forelse($students as $student)

                <tr class="border-t">

                    <td class="px-6 py-5">
                        {{ $student->name }}
                    </td>

                    <td class="px-6 py-5">
                        {{ $student->nim }}
                    </td>

                    <td class="px-6 py-5">
                        {{ $student->created_at->format('d M Y') }}
                    </td>

                    <td class="px-6 py-5 text-center">

                        <form
                            action="{{ route('admin.accounts.students.destroy',$student) }}"
                            method="POST">

                            @csrf
                            @method('DELETE')

                            <button
                                onclick="return confirm('Hapus akun student ini?')"
                                class="bg-red-600 text-white px-4 py-2 rounded-xl hover:bg-red-700 transition">

                                Delete

                            </button>

                        </form>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="4"
                        class="text-center py-10 text-gray-400">

                        Belum ada akun student.

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

</div>

@endsection