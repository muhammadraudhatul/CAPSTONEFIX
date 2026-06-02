@extends('admin.layouts.app')

@section('content')

<div class="mb-8">

    <h1 class="text-3xl font-bold text-gray-800">
        Kelola Akun Admin
    </h1>

    <p class="text-gray-500 mt-2">
        Tambahkan akun admin baru untuk mengakses sistem.
    </p>

</div>

@if(session('success'))

    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
        {{ session('success') }}
    </div>

@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- FORM -->

    <div class="lg:col-span-1">

        <div class="bg-white rounded-3xl shadow p-6">

            <h2 class="font-bold text-lg mb-6">
                Tambah Admin
            </h2>

            <form
                action="{{ route('admin.accounts.admins.store') }}"
                method="POST">

                @csrf

                <div class="mb-4">

                    <label class="block mb-2 text-sm font-medium">
                        Nama
                    </label>

                    <input
                        type="text"
                        name="name"
                        class="w-full border rounded-xl px-4 py-3">

                </div>

                <div class="mb-4">

                    <label class="block mb-2 text-sm font-medium">
                        Username
                    </label>

                    <input
                        type="text"
                        name="nim"
                        class="w-full border rounded-xl px-4 py-3">

                </div>

                <div class="mb-4">

                    <label class="block mb-2 text-sm font-medium">
                        Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="w-full border rounded-xl px-4 py-3">

                </div>

                <div class="mb-6">

                    <label class="block mb-2 text-sm font-medium">
                        Konfirmasi Password
                    </label>

                    <input
                        type="password"
                        name="password_confirmation"
                        class="w-full border rounded-xl px-4 py-3">

                </div>

                <button
                    type="submit"
                    class="w-full bg-green-600 text-white py-3 rounded-xl hover:bg-green-700 transition">

                    Tambah Admin

                </button>

            </form>

        </div>

    </div>

    <!-- LIST ADMIN -->

    <div class="lg:col-span-2">

        <div class="bg-white rounded-3xl shadow overflow-hidden">

            <table class="w-full">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="px-6 py-4 text-left">
                            Nama
                        </th>

                        <th class="px-6 py-4 text-left">
                            Username
                        </th>

                        <th class="px-6 py-4 text-left">
                            Status
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @foreach($admins as $admin)

                        <tr class="border-t">

                            <td class="px-6 py-5">

                                {{ $admin->name }}

                            </td>

                            <td class="px-6 py-5">

                                {{ $admin->nim }}

                            </td>

                            <td class="px-6 py-5">

                                @if($admin->nim === 'admin')

                                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-xl text-sm">

                                        Admin Utama

                                    </span>

                                @else

                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-xl text-sm">

                                        Admin

                                    </span>

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