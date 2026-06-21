<?php

namespace App\Console\Commands;

use App\Models\AiRun;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class RunAiAnalysis extends Command
{
    protected $signature = 'ai:run-analysis {--trigger=manual}';

    protected $description = 'Run full AI analysis pipeline: export inputs, run Python pipeline, and import outputs';

    public function handle(): int
    {
        $trigger = $this->option('trigger');

        $aiRun = AiRun::create([
            'status' => 'running',
            'trigger_source' => $trigger,
            'message' => 'AI analysis started.',
            'started_at' => now(),
        ]);

        try {
            $this->info('Starting AI analysis pipeline...');

            $this->info('Step 1/3: Exporting AI inputs...');
            $exportCode = $this->call('ai:export-inputs');

            if ($exportCode !== self::SUCCESS) {
                throw new \Exception('Failed to export AI inputs.');
            }

            $this->info('Step 2/3: Running Python AI pipeline...');

            $pythonPath = env('PYTHON_PATH', 'python');

            $scriptPath = base_path('ai_pipeline/run_ai_pipeline.py');

            $process = new Process([
                $pythonPath,
                $scriptPath,
            ]);

            $process->setWorkingDirectory(base_path());
            $process->setTimeout(300);

            $process->run(function ($type, $buffer) {
                echo $buffer;
            });

            if (!$process->isSuccessful()) {
                throw new \Exception(
                    "Python pipeline failed:\n" . $process->getErrorOutput()
                );
            }

            $this->info('Step 3/3: Importing AI outputs...');
            $importCode = $this->call('ai:import-outputs');

            if ($importCode !== self::SUCCESS) {
                throw new \Exception('Failed to import AI outputs.');
            }

            $aiRun->update([
                'status' => 'success',
                'message' => 'AI analysis completed successfully.',
                'finished_at' => now(),
            ]);

            $this->info('AI analysis pipeline completed successfully.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $aiRun->update([
                'status' => 'failed',
                'message' => $e->getMessage(),
                'finished_at' => now(),
            ]);

            $this->error('AI analysis pipeline failed.');
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}