<?php

namespace App\Models;

use Database\Factories\MonitorHistoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'monitor_id',
    'status_code',
    'response_time_ms',
    'is_up',
    'checked_at',
])]
class MonitorHistory extends Model
{
    /** @use HasFactory<MonitorHistoryFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Monitor, $this>
     */
    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status_code' => 'integer',
            'response_time_ms' => 'integer',
            'is_up' => 'boolean',
            'checked_at' => 'datetime',
        ];
    }
}
