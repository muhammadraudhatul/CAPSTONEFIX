<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowings', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->date('borrow_date');

            $table->string('room');

            $table->string('time_slot');

            $table->text('purpose');

            $table->integer('participants');

            $table->boolean('with_lecturer')
                  ->default(false);

            $table->string('lecturer_name')
                  ->nullable();

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'completed'
            ])->default('pending');

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};