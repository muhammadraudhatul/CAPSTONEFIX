<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowing_items', function (Blueprint $table) {

            $table->id();

            $table->foreignId('borrowing_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('item_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('qty');

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowing_items');
    }
};