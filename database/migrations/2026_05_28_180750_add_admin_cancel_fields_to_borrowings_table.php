<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {

            $table->foreignId('cancelled_by')
                ->nullable()
                ->after('status')
                ->constrained('users')
                ->nullOnDelete();

            $table->text('cancel_reason')
                ->nullable()
                ->after('cancelled_by');

        });
    }

    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {

            $table->dropConstrainedForeignId(
                'cancelled_by'
            );

            $table->dropColumn(
                'cancel_reason'
            );

        });
    }
};