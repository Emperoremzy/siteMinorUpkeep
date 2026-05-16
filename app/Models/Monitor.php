<?php

namespace App\Models;

use Database\Factories\MonitorFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'url',
    'check_interval',
    'threshold',
    'status',
    'last_checked_at',
    'uptime_percentage',
])]
class Monitor extends Model
{
    /** @use HasFactory<MonitorFactory> */
    use HasFactory;

    /**
     * @return HasMany<MonitorHistory, $this>
     */
    public function histories(): HasMany
    {
        return $this->hasMany(MonitorHistory::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'check_interval' => 'integer',
            'threshold' => 'integer',
            'last_checked_at' => 'datetime',
            'uptime_percentage' => 'float',
        ];
    }
}
