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
        Schema::create('ai_room_prediction_summaries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('room_id')
                ->nullable()
                ->constrained('rooms')
                ->nullOnDelete();

            $table->string('room_name');

            $table->integer('history_count')->default(0);

            $table->string('data_status')->default('belum_ada_data');
            // belum_ada_data, data_terbatas, cukup_data

            $table->integer('total_slot')->default(0);

            $table->integer('predicted_used_slot')->default(0);

            $table->decimal('average_probability_used', 8, 3)->default(0);

            $table->decimal('predicted_occupancy_rate', 8, 3)->default(0);

            $table->string('predicted_occupancy_status')->nullable();
            // rendah, sedang, tinggi

            $table->integer('predicted_peak_hour')->nullable();

            $table->decimal('peak_hour_probability', 8, 3)->nullable();

            $table->timestamps();

            $table->index('room_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_room_prediction_summaries');
    }
};
