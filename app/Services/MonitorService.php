<?php

namespace App\Services;

use App\Models\Monitor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MonitorService
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): Monitor
    {
        return Monitor::query()->create($attributes);
    }

    /**
     * @return Collection<int, Monitor>
     */
    public function all(): Collection
    {
        return Monitor::query()
            ->latest('id')
            ->get();
    }

    public function find(int $id): ?Monitor
    {
        return Monitor::query()->find($id);
    }

    public function paginatedHistory(Monitor $monitor, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        return $monitor->histories()
            ->orderByDesc('checked_at')
            ->paginate(perPage: $perPage, page: $page);
    }
}
