<?php

namespace App\Http\Controllers;

use App\Models\EventLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventLogController extends Controller
{
    /**
     * Retrieve event logs with pagination, filters, and sorting.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $perPage = 16;
            $query = EventLog::query();

            // Date Range Filter
            if ($request->has('startDate') && $request->has('endDate')) {
                $startDate = $request->input('startDate');
                $endDate = $request->input('endDate');
                $query->whereBetween('event_time', [$startDate, $endDate]);
            }

            // User Filter
            if ($request->has('user')) {
                $user = $request->input('user');
                $query->where('user_id', $user);
            }

            // Event Type Filter
            if ($request->has('eventType')) {
                $eventType = $request->input('eventType');
                $query->where('event_type', $eventType);
            }

            // Sorting
            $sortBy = $request->input('sortBy', 'event_time'); // Default sorting by event_time
            $sortDirection = $request->input('sortDirection', 'desc'); // Default descending order
            $query->orderBy($sortBy, $sortDirection);

            // Paginate the results
            $eventLogs = $query->paginate($perPage);

            return response()->json($eventLogs);

        } catch (\Exception $e) {
            Log::error('Error fetching event logs: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch event logs'], 500);
        }
    }
}