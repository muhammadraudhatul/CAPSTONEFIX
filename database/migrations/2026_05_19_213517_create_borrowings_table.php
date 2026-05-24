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

            $table->foreignId('room_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('borrow_date');

            $table->string('day');

            $table->string('time_slot');

            $table->text('purpose');

            $table->integer('total_people');

            $table->boolean('with_lecturer')
                ->default(false);

            $table->string('lecturer_name')
                ->nullable();

            $table->enum('status', [

                'PENDING',
                'APPROVED',
                'WAITING_RETURN',
                'COMPLETED',
                'REJECTED',
                'CANCELLED',

            ])->default('PENDING');

            $table->timestamp('returned_at')
                ->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};