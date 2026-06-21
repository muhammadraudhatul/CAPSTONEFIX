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
        Schema::create('ai_model_evaluations', function (Blueprint $table) {
            $table->id();

            $table->string('model_name');

            $table->string('model_type');
            // barang, ruangan, anomali

            $table->decimal('mae', 10, 6)->nullable();
            $table->decimal('rmse', 10, 6)->nullable();
            $table->decimal('r2', 10, 6)->nullable();

            $table->decimal('accuracy', 10, 6)->nullable();
            $table->decimal('precision', 10, 6)->nullable();
            $table->decimal('recall', 10, 6)->nullable();
            $table->decimal('f1', 10, 6)->nullable();

            $table->json('raw_metrics')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_model_evaluations');
    }
};
