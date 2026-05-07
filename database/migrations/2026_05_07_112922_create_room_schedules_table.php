<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_schedules', function (Blueprint $table) {

            $table->id();

            $table->foreignId('room_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->date('week_start');

            $table->enum('day', [
                'Senin',
                'Selasa',
                'Rabu',
                'Kamis',
                'Jumat',
            ]);

            $table->string('time_slot');

            $table->boolean('available')
                  ->default(true);

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_schedules');
    }
};