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
        Schema::create('ai_item_predictions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('item_id')
                ->nullable()
                ->constrained('items')
                ->nullOnDelete();

            $table->foreignId('room_id')
                ->nullable()
                ->constrained('rooms')
                ->nullOnDelete();

            $table->string('item_name');
            $table->string('item_type')->nullable();

            $table->string('room_name')->nullable();

            $table->integer('stock')->default(0);
            $table->integer('minimum_stock')->default(0);

            $table->integer('history_count')->default(0);

            $table->string('data_status')->default('belum_ada_data');
            // belum_ada_data, data_terbatas, cukup_data

            $table->integer('last_used_qty')->default(0);

            $table->decimal('predicted_next_usage', 10, 2)->default(0);

            $table->string('prediction_status')->nullable();
            // rendah, sedang, tinggi

            $table->timestamps();

            $table->index(['item_id', 'room_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_item_predictions');
    }
};
