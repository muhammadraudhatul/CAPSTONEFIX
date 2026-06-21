<?php

namespace App\Jobs;

use App\Models\AiRun;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class RunAiAnalysisJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $trigger;

    public int $tries = 1;

    public int $timeout = 600;

    public function __construct(string $trigger = 'queue')
    {
        $this->trigger = $trigger;
    }

    public function handle(): void
    {
        Artisan::call('ai:run-analysis', [
            '--trigger' => $this->trigger,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        AiRun::create([
            'status' => 'failed',
            'trigger_source' => $this->trigger,
            'message' => $exception->getMessage(),
            'started_at' => now(),
            'finished_at' => now(),
        ]);
    }
}