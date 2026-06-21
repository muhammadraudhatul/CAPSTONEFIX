<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExportAiInputs extends Command
{
    protected $signature = 'ai:export-inputs';

    protected $description = 'Export Laravel database data into CSV files for AI pipeline';

    public function handle(): int
    {
        $dir = storage_path('app/ai/input');

        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $this->info('Exporting AI input data...');

        $this->exportBorrowings($dir);
        $this->exportBorrowingItems($dir);
        $this->exportItems($dir);
        $this->exportRooms($dir);
        $this->exportRoomSchedules($dir);

        $this->info('AI input data exported successfully.');

        return self::SUCCESS;
    }

    private function writeCsv(string $path, array $headers, $rows): void
    {
        $file = fopen($path, 'w');

        fputcsv($file, $headers);

        foreach ($rows as $row) {
            $line = [];

            foreach ($headers as $header) {
                $line[] = $row->{$header} ?? null;
            }

            fputcsv($file, $line);
        }

        fclose($file);
    }

    private function exportBorrowings(string $dir): void
    {
        $rows = DB::table('borrowings')
            ->where('status', 'COMPLETED')
            ->select([
                'id',
                'user_id',
                'room_id',
                'borrow_date',
                'day',
                'time_slot',
                'purpose',
                'total_people',
                'with_lecturer',
                'lecturer_name',
                'status',
                'cancelled_by',
                'cancel_reason',
                'returned_at',
                'created_at',
                'updated_at',
            ])
            ->orderBy('borrow_date')
            ->orderBy('id')
            ->get();

        $this->writeCsv(
            $dir . '/borrowings.csv',
            [
                'id',
                'user_id',
                'room_id',
                'borrow_date',
                'day',
                'time_slot',
                'purpose',
                'total_people',
                'with_lecturer',
                'lecturer_name',
                'status',
                'cancelled_by',
                'cancel_reason',
                'returned_at',
                'created_at',
                'updated_at',
            ],
            $rows
        );

        $this->info('borrowings.csv exported: ' . $rows->count() . ' rows');
    }

    private function exportBorrowingItems(string $dir): void
    {
        $rows = DB::table('borrowing_items')
            ->join('borrowings', 'borrowings.id', '=', 'borrowing_items.borrowing_id')
            ->where('borrowings.status', 'completed')
            ->select([
                'borrowing_items.id',
                'borrowing_items.borrowing_id',
                'borrowing_items.item_id',
                'borrowing_items.qty',
                'borrowing_items.returned_qty',
                'borrowing_items.created_at',
                'borrowing_items.updated_at',
            ])
            ->orderBy('borrowing_items.borrowing_id')
            ->orderBy('borrowing_items.id')
            ->get();

        $this->writeCsv(
            $dir . '/borrowing_items.csv',
            [
                'id',
                'borrowing_id',
                'item_id',
                'qty',
                'returned_qty',
                'created_at',
                'updated_at',
            ],
            $rows
        );

        $this->info('borrowing_items.csv exported: ' . $rows->count() . ' rows');
    }

    private function exportItems(string $dir): void
    {
        $rows = DB::table('items')
            ->whereNull('deleted_at')
            ->select([
                'id',
                'name',
                'type',
                'room_id',
                'location',
                'unit',
                'stock',
                'minimum_stock',
                'description',
                'created_at',
                'updated_at',
            ])
            ->orderBy('id')
            ->get();

        $this->writeCsv(
            $dir . '/items.csv',
            [
                'id',
                'name',
                'type',
                'room_id',
                'location',
                'unit',
                'stock',
                'minimum_stock',
                'description',
                'created_at',
                'updated_at',
            ],
            $rows
        );

        $this->info('items.csv exported: ' . $rows->count() . ' rows');
    }

    private function exportRooms(string $dir): void
    {
        $rows = DB::table('rooms')
            ->select([
                'id',
                'name',
                'capacity',
                'created_at',
                'updated_at',
            ])
            ->orderBy('id')
            ->get();

        $this->writeCsv(
            $dir . '/rooms.csv',
            [
                'id',
                'name',
                'capacity',
                'created_at',
                'updated_at',
            ],
            $rows
        );

        $this->info('rooms.csv exported: ' . $rows->count() . ' rows');
    }

    private function exportRoomSchedules(string $dir): void
    {
        $rows = DB::table('room_schedules')
            ->select([
                'id',
                'room_id',
                'week_start',
                'day',
                'time_slot',
                'available',
                'created_at',
                'updated_at',
            ])
            ->orderBy('room_id')
            ->orderBy('week_start')
            ->orderBy('id')
            ->get();

        $this->writeCsv(
            $dir . '/room_schedules.csv',
            [
                'id',
                'room_id',
                'week_start',
                'day',
                'time_slot',
                'available',
                'created_at',
                'updated_at',
            ],
            $rows
        );

        $this->info('room_schedules.csv exported: ' . $rows->count() . ' rows');
    }
}