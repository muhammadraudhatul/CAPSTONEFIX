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
        Schema::create('ai_room_anomalies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('room_id')
                ->nullable()
                ->constrained('rooms')
                ->nullOnDelete();

            $table->date('date');

            $table->string('room_name');

            $table->integer('total_borrowing')->default(0);

            $table->decimal('total_duration', 10, 2)->default(0);

            $table->decimal('occupancy_rate', 8, 3)->default(0);

            $table->decimal('anomaly_score', 12, 6)->default(0);

            $table->timestamps();

            $table->index(['room_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_room_anomalies');
    }
};
