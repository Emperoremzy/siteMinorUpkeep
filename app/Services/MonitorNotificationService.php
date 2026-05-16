<?php

namespace App\Services;

use App\Mail\MonitorStatusChanged;
use App\Models\Monitor;
use Illuminate\Support\Facades\Mail;
use Throwable;

class MonitorNotificationService
{
    public function sendStatusTransitionNotification(Monitor $monitor, string $previousStatus, string $currentStatus): void
    {
        if (! $this->shouldNotify($previousStatus, $currentStatus)) {
            return;
        }

        $recipient = config('uptime.notifications.to');

        if (! is_string($recipient) || $recipient === '') {
            return;
        }

        try {
            Mail::to($recipient)->send(new MonitorStatusChanged(
                monitor: $monitor,
                previousStatus: $previousStatus,
                currentStatus: $currentStatus,
            ));
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    protected function shouldNotify(string $previousStatus, string $currentStatus): bool
    {
        if ($currentStatus === 'down' && in_array($previousStatus, ['up', 'pending'], true)) {
            return true;
        }

        return $previousStatus === 'down' && $currentStatus === 'up';
    }
}
