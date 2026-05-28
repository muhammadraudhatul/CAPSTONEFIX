@if ($errors->any())

    <div class="mb-8 bg-red-50 border border-red-200
                text-red-700 rounded-2xl p-5">

        <div class="flex items-start gap-3">

            <div class="text-2xl">
                ⚠️
            </div>

            <div>

                <h3 class="font-bold text-lg">
                    Terjadi Kesalahan
                </h3>

                <ul class="list-disc ml-5 mt-3 space-y-1">

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        </div>

    </div>

@endif