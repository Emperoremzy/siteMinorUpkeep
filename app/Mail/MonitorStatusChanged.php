<?php

namespace App\Mail;

use App\Models\Monitor;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MonitorStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Monitor $monitor,
        public readonly string $previousStatus,
        public readonly string $currentStatus,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Monitor {$this->currentStatus}: {$this->monitor->url}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.monitors.status-changed',
        );
    }
}
