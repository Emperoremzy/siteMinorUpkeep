<?php

namespace App\Services;

use App\Models\Monitor;
use App\Models\MonitorHistory;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Throwable;

class UptimeCheckerService
{
    public function check(Monitor $monitor): MonitorHistory
    {
        $checkedAt = Carbon::now();
        $startedAt = hrtime(true);

        try {
            $response = $this->sendRequest($monitor);

            $history = $this->recordResult(
                monitor: $monitor,
                statusCode: $response->status(),
                responseTimeMs: $this->elapsedMilliseconds($startedAt),
                checkedAt: $checkedAt,
            );
        } catch (Throwable) {
            $history = $this->recordFailure(
                monitor: $monitor,
                checkedAt: $checkedAt,
            );
        }

        $this->updateMonitorMetrics($monitor, $checkedAt);

        return $history;
    }

    protected function sendRequest(Monitor $monitor): Response
    {
        return Http::withOptions([
            'connect_timeout' => 5,
            'timeout' => 10,
            'allow_redirects' => true,
        ])->get($monitor->url);
    }

    protected function recordResult(
        Monitor $monitor,
        int $statusCode,
        int $responseTimeMs,
        Carbon $checkedAt,
    ): MonitorHistory {
        return $monitor->histories()->create([
            'status_code' => $statusCode,
            'response_time_ms' => $responseTimeMs,
            'is_up' => $this->isSuccessfulStatusCode($statusCode),
            'checked_at' => $checkedAt,
        ]);
    }

    protected function recordFailure(Monitor $monitor, Carbon $checkedAt): MonitorHistory
    {
        return $monitor->histories()->create([
            'status_code' => 0,
            'response_time_ms' => null,
            'is_up' => false,
            'checked_at' => $checkedAt,
        ]);
    }

    protected function updateMonitorMetrics(Monitor $monitor, Carbon $checkedAt): void
    {
        $monitor->forceFill([
            'last_checked_at' => $checkedAt,
            'uptime_percentage' => $this->calculateUptimePercentage($monitor),
        ])->save();
    }

    public function calculateUptimePercentage(Monitor $monitor): float
    {
        $totalChecks = $monitor->histories()->count();

        if ($totalChecks === 0) {
            return 0.0;
        }

        $successfulChecks = $monitor->histories()
            ->where('is_up', true)
            ->count();

        return round(($successfulChecks / $totalChecks) * 100, 2);
    }

    protected function isSuccessfulStatusCode(int $statusCode): bool
    {
        return $statusCode >= 200 && $statusCode < 400;
    }

    protected function elapsedMilliseconds(int $startedAt): int
    {
        return max(1, (int) round((hrtime(true) - $startedAt) / 1_000_000));
    }
}
