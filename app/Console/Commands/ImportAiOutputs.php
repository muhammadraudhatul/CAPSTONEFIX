<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AiItemPrediction;
use App\Models\AiRoomSlotPrediction;
use App\Models\AiRoomPredictionSummary;
use App\Models\AiRoomAnomaly;
use App\Models\AiModelEvaluation;

class ImportAiOutputs extends Command
{
    protected $signature = 'ai:import-outputs';

    protected $description = 'Import AI output CSV files into MySQL tables';

    public function handle(): int
    {
        $this->info('Importing AI output data...');

        $this->importItemPredictions();
        $this->importRoomSlotPredictions();
        $this->importRoomPredictionSummaries();
        $this->importRoomAnomalies();
        $this->importModelEvaluations();

        $this->info('AI output data imported successfully.');

        return self::SUCCESS;
    }

    private function readCsv(string $filename): array
    {
        $path = storage_path('app/ai/output/' . $filename);

        if (!file_exists($path)) {
            $this->warn("File not found: {$filename}");
            return [];
        }

        $file = fopen($path, 'r');

        if ($file === false) {
            $this->warn("Cannot open file: {$filename}");
            return [];
        }

        $headers = fgetcsv($file);

        if (!$headers) {
            fclose($file);
            return [];
        }

        $headers = array_map(function ($header) {
            return trim($header);
        }, $headers);

        $rows = [];

        while (($data = fgetcsv($file)) !== false) {
            if (count($data) !== count($headers)) {
                continue;
            }

            $rows[] = array_combine($headers, $data);
        }

        fclose($file);

        return $rows;
    }

    private function value(array $row, string $key, $default = null)
    {
        return array_key_exists($key, $row) && $row[$key] !== ''
            ? $row[$key]
            : $default;
    }

    private function intValue(array $row, string $key, int $default = 0): int
    {
        return (int) $this->value($row, $key, $default);
    }

    private function floatValue(array $row, string $key, float $default = 0): float
    {
        return (float) $this->value($row, $key, $default);
    }

    private function importItemPredictions(): void
    {
        AiItemPrediction::truncate();

        $rows = $this->readCsv('prediksi_barang_dashboard.csv');

        foreach ($rows as $row) {
            AiItemPrediction::create([
                'item_id' => $this->value($row, 'item_id'),
                'room_id' => $this->value($row, 'room_id'),

                'item_name' => $this->value($row, 'item_name', '-'),
                'item_type' => $this->value($row, 'item_type'),

                'room_name' => $this->value($row, 'room_name'),

                'stock' => $this->intValue($row, 'stock'),
                'minimum_stock' => $this->intValue($row, 'minimum_stock'),

                'history_count' => $this->intValue($row, 'history_count'),
                'data_status' => $this->value($row, 'data_status', 'belum_ada_data'),

                'last_used_qty' => $this->intValue($row, 'last_used_qty'),

                'predicted_next_usage' => $this->floatValue($row, 'predicted_next_usage'),

                'prediction_status' => $this->value($row, 'prediction_status', 'rendah'),

                'predicted_semester_usage' => $this->floatValue($row, 'predicted_semester_usage'),
                'estimated_semester_borrowing_count' => $this->floatValue($row, 'estimated_semester_borrowing_count'),
                'semester_stock_gap' => $this->floatValue($row, 'semester_stock_gap'),

                'semester_stock_status' => $this->value($row, 'semester_stock_status', 'Belum ada data'),
                'semester_recommendation' => $this->value($row, 'semester_recommendation'),
                'semester_reason' => $this->value($row, 'semester_reason'),
            ]);
        }

        $this->info('ai_item_predictions imported: ' . count($rows) . ' rows');
    }

    private function importRoomSlotPredictions(): void
    {
        AiRoomSlotPrediction::truncate();

        $rows = $this->readCsv('prediksi_slot_ruangan_dashboard.csv');

        foreach ($rows as $row) {
            AiRoomSlotPrediction::create([
                'room_id' => $this->value($row, 'room_id'),

                'room_name' => $this->value($row, 'room_name', '-'),

                'jam_slot' => $this->intValue($row, 'jam_slot'),
                'time_slot' => $this->value($row, 'time_slot'),

                'day' => $this->value($row, 'day'),
                'month' => $this->value($row, 'month'),

                'history_count' => $this->intValue($row, 'history_count'),
                'data_status' => $this->value($row, 'data_status', 'belum_ada_data'),

                'current_status' => $this->value($row, 'current_status'),

                'probability_used' => $this->floatValue($row, 'probability_used'),

                'predicted_status' => $this->value($row, 'predicted_status', 'Tidak Terpakai'),

                'confidence_score' => $this->floatValue($row, 'confidence_score'),
            ]);
        }

        $this->info('ai_room_slot_predictions imported: ' . count($rows) . ' rows');
    }

    private function importRoomPredictionSummaries(): void
    {
        AiRoomPredictionSummary::truncate();

        $rows = $this->readCsv('ringkasan_prediksi_ruangan_dashboard.csv');

        foreach ($rows as $row) {
            AiRoomPredictionSummary::create([
                'room_id' => $this->value($row, 'room_id'),

                'room_name' => $this->value($row, 'room_name', '-'),

                'history_count' => $this->intValue($row, 'history_count'),
                'data_status' => $this->value($row, 'data_status', 'belum_ada_data'),

                'total_slot' => $this->intValue($row, 'total_slot'),
                'predicted_used_slot' => $this->intValue($row, 'predicted_used_slot'),

                'average_probability_used' => $this->floatValue($row, 'average_probability_used'),

                'predicted_occupancy_rate' => $this->floatValue($row, 'predicted_occupancy_rate'),

                'predicted_occupancy_status' => $this->value($row, 'predicted_occupancy_status', 'rendah'),

                'predicted_peak_hour' => $this->value($row, 'predicted_peak_hour'),

                'peak_hour_probability' => $this->value($row, 'peak_hour_probability'),
            ]);
        }

        $this->info('ai_room_prediction_summaries imported: ' . count($rows) . ' rows');
    }

    private function importRoomAnomalies(): void
    {
        AiRoomAnomaly::truncate();

        $rows = $this->readCsv('top_anomali_ruangan_dashboard.csv');

        foreach ($rows as $row) {
            $date = $this->value($row, 'date');

            if (!$date) {
                continue;
            }

            AiRoomAnomaly::create([
                'room_id' => $this->value($row, 'room_id'),

                'room_name' => $this->value($row, 'room_name', '-'),

                'date' => $date,

                'total_borrowing' => $this->intValue($row, 'total_borrowing'),

                'total_duration' => $this->floatValue($row, 'total_duration'),

                'occupancy_rate' => $this->floatValue($row, 'occupancy_rate'),

                'anomaly_score' => $this->floatValue($row, 'anomaly_score'),
            ]);
        }

        $this->info('ai_room_anomalies imported: ' . count($rows) . ' rows');
    }

    private function importModelEvaluations(): void
    {
        AiModelEvaluation::truncate();

        $files = [
            'evaluasi_model_barang.csv' => 'barang',
            'evaluasi_model_slot_ruangan.csv' => 'ruangan',
            'evaluasi_model_anomali.csv' => 'anomali',
        ];

        $total = 0;

        foreach ($files as $filename => $modelType) {
            $rows = $this->readCsv($filename);

            foreach ($rows as $row) {
                AiModelEvaluation::create([
                    'model_name' => $this->value($row, 'model', $filename),
                    'model_type' => $this->value($row, 'model_type', $modelType),

                    'mae' => $this->value($row, 'MAE'),
                    'rmse' => $this->value($row, 'RMSE'),
                    'r2' => $this->value($row, 'R2'),

                    'accuracy' => $this->value($row, 'Accuracy'),
                    'precision' => $this->value($row, 'Precision'),
                    'recall' => $this->value($row, 'Recall'),
                    'f1' => $this->value($row, 'F1'),

                    'raw_metrics' => $row,
                ]);

                $total++;
            }
        }

        $this->info('ai_model_evaluations imported: ' . $total . ' rows');
    }
}