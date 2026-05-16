<?php

namespace App\Console\Commands;

use App\Models\Monitor;
use App\Services\MonitorService;
use App\Services\UptimeCheckerService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Throwable;

class CheckMonitorsCommand extends Command
{
    protected $signature = 'app:check-monitors {--chunk=100 : Number of monitors to process per chunk}';

    protected $description = 'Check due uptime monitors and record their latest status.';

    public function handle(MonitorService $monitors, UptimeCheckerService $checker): int
    {
        $now = Carbon::now();
        $checked = 0;
        $failed = 0;
        $chunkSize = max(1, (int) $this->option('chunk'));

        Monitor::query()
            ->where(function ($query) use ($now): void {
                $query
                    ->whereNull('last_checked_at')
                    ->orWhere('last_checked_at', '<=', $now->copy()->subMinute());
            })
            ->orderBy('id')
            ->chunkById($chunkSize, function ($dueCandidates) use ($checker, $monitors, $now, &$checked, &$failed): void {
                foreach ($dueCandidates as $monitor) {
                    if (! $monitors->isDueForCheck($monitor, $now)) {
                        continue;
                    }

                    try {
                        $checker->check($monitor);
                        $checked++;
                    } catch (Throwable $exception) {
                        report($exception);
                        $failed++;
                    }
                }
            });

        $this->info("Checked {$checked} monitor(s).");

        if ($failed > 0) {
            $this->warn("Failed to process {$failed} monitor(s).");
        }

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
