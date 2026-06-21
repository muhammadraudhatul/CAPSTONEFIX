<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_item_predictions', function (Blueprint $table) {
            $table->decimal('predicted_semester_usage', 12, 2)
                ->default(0)
                ->after('prediction_status');

            $table->decimal('estimated_semester_borrowing_count', 8, 2)
                ->default(0)
                ->after('predicted_semester_usage');

            $table->decimal('semester_stock_gap', 12, 2)
                ->default(0)
                ->after('estimated_semester_borrowing_count');

            $table->string('semester_stock_status')
                ->default('Belum ada data')
                ->after('semester_stock_gap');

            $table->string('semester_recommendation')
                ->nullable()
                ->after('semester_stock_status');

            $table->text('semester_reason')
                ->nullable()
                ->after('semester_recommendation');
        });
    }

    public function down(): void
    {
        Schema::table('ai_item_predictions', function (Blueprint $table) {
            $table->dropColumn([
                'predicted_semester_usage',
                'estimated_semester_borrowing_count',
                'semester_stock_gap',
                'semester_stock_status',
                'semester_recommendation',
                'semester_reason',
            ]);
        });
    }
};