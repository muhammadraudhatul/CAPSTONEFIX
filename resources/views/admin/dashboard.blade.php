@extends('admin.layouts.app')

@section('content')

<div>

    <h1 class="text-5xl font-bold text-gray-900">
        Dashboard Overview
    </h1>

    <p class="text-gray-600 mt-3 text-lg">
        Selamat datang di sistem manajemen laboratorium
    </p>

</div>

<!-- Cards -->
<div class="grid grid-cols-4 gap-6 mt-10">

    <div class="bg-white rounded-3xl shadow-sm p-8 border-l-4 border-blue-500">

        <div class="text-5xl">
            📦
        </div>

        <h2 class="mt-6 text-gray-500">
            Total Item Inventory
        </h2>

        <p class="text-5xl font-bold mt-3">
            -
        </p>

    </div>

    <div class="bg-white rounded-3xl shadow-sm p-8 border-l-4 border-green-500">

        <div class="text-5xl">
            🧪
        </div>

        <h2 class="mt-6 text-gray-500">
            Total Stok
        </h2>

        <p class="text-5xl font-bold mt-3">
            -
        </p>

    </div>

    <div class="bg-white rounded-3xl shadow-sm p-8 border-l-4 border-purple-500">

        <div class="text-5xl">
            🚪
        </div>

        <h2 class="mt-6 text-gray-500">
            Ruangan
        </h2>

        <p class="text-5xl font-bold mt-3">
            -
        </p>

    </div>

    <div class="bg-white rounded-3xl shadow-sm p-8 border-l-4 border-orange-500">

        <div class="text-5xl">
            ⚠️
        </div>

        <h2 class="mt-6 text-gray-500">
            Stok Rendah
        </h2>

        <p class="text-5xl font-bold mt-3">
            -
        </p>

    </div>

</div>

<!-- Bottom -->
<div class="grid grid-cols-2 gap-8 mt-10">

    <!-- Left -->
    <div class="bg-white rounded-3xl shadow-sm p-8 min-h-[500px]">

        <h2 class="text-3xl font-bold">
            Item Stok Rendah
        </h2>

    </div>

    <!-- Right -->
    <div class="bg-white rounded-3xl shadow-sm p-8 min-h-[500px]">

        <h2 class="text-3xl font-bold">
            Status Ruangan
        </h2>

    </div>

</div>

@endsection