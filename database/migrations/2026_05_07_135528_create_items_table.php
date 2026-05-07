<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {

            $table->id();

            $table->string('name');

            $table->enum('type', [
                'tool',
                'material'
            ]);

            $table->foreignId('room_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('location');

            $table->string('unit');

            $table->integer('stock')
                ->default(0);

            $table->integer('minimum_stock')
                ->default(0);

            $table->text('description')
                ->nullable();

            $table->softDeletes();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};