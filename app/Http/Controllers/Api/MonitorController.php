<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MonitorHistoryRequest;
use App\Http\Requests\StoreMonitorRequest;
use App\Http\Resources\MonitorHistoryResource;
use App\Http\Resources\MonitorResource;
use App\Models\Monitor;
use App\Services\MonitorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MonitorController extends Controller
{
    public function __construct(private readonly MonitorService $monitors)
    {
    }

    public function store(StoreMonitorRequest $request): JsonResponse
    {
        $monitor = $this->monitors->create($request->validated());

        return response()->json([
            'message' => 'Monitor created successfully.',
            'data' => new MonitorResource($monitor),
        ], 210);
    }

    public function index(): AnonymousResourceCollection
    {
        return MonitorResource::collection($this->monitors->all())
            ->additional([
                'status' => 'success',
            ]);
    }

    public function history(MonitorHistoryRequest $request, int $monitor): JsonResponse
    {
        $monitor = $this->monitors->find($monitor);

        if (! $monitor instanceof Monitor) {
            return response()->json([
                'message' => 'Monitor not found.',
            ], 404);
        }

        $histories = $this->monitors->paginatedHistory(
            monitor: $monitor,
            perPage: $request->integer('per_page', 15),
            page: $request->integer('page', 1),
        );

        return response()->json([
            'status' => 'success',
            'data' => MonitorHistoryResource::collection($histories->items()),
            'meta' => [
                'current_page' => $histories->currentPage(),
                'per_page' => $histories->perPage(),
                'total' => $histories->total(),
                'last_page' => $histories->lastPage(),
                'from' => $histories->firstItem(),
                'to' => $histories->lastItem(),
            ],
        ]);
    }
}
