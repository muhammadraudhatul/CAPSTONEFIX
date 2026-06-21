<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ai_room_slot_predictions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('room_id')
                ->nullable()
                ->constrained('rooms')
                ->nullOnDelete();

            $table->string('room_name');

            $table->integer('jam_slot');
            $table->string('time_slot')->nullable();

            $table->integer('day')->nullable();
            $table->integer('month')->nullable();

            $table->integer('history_count')->default(0);

            $table->string('data_status')->default('belum_ada_data');
            // belum_ada_data, data_terbatas, cukup_data

            $table->string('current_status')->nullable();
            // Terpakai / Tidak Terpakai

            $table->decimal('probability_used', 8, 3)->default(0);

            $table->string('predicted_status')->default('Tidak Terpakai');
            // Terpakai / Tidak Terpakai

            $table->decimal('confidence_score', 8, 3)->default(0);

            $table->timestamps();

            $table->index(['room_id', 'jam_slot']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_room_slot_predictions');
    }
};
