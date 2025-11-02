<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ParallelSession;
use App\Models\Conference;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as RequestFacade;
use Inertia\Inertia;
use Inertia\Response;

class ParallelSessionManagementController extends Controller
{
    /**
     * Display a listing of parallel sessions
     */
    public function index(): Response
    {
        $filters = RequestFacade::only('conference_id');
        $perPage = RequestFacade::input('per_page', 15); // Default 15, bisa diubah via parameter
        
        // Build query with filters
        $query = ParallelSession::query()
            ->with(['audience.conference', 'room'])
            ->whereHas('audience.conference');

        // Apply conference filter
        if (!empty($filters['conference_id'])) {
            $query->whereHas('audience', function ($q) use ($filters) {
                $q->where('conference_id', $filters['conference_id']);
            });
        }

        // Get parallel sessions with pagination
        $parallelSessions = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends(RequestFacade::all());

        // Get all conferences for filter dropdown
        $conferences = Conference::whereNull('deleted_at')
            ->orderBy('name')
            ->get(['id', 'name']);

        // Get summary counts
        $summaryQuery = ParallelSession::query()->whereHas('audience.conference');
        
        if (!empty($filters['conference_id'])) {
            $summaryQuery->whereHas('audience', function ($q) use ($filters) {
                $q->where('conference_id', $filters['conference_id']);
            });
        }

        $summary = [
            'total' => $summaryQuery->count(),
            'this_month' => (clone $summaryQuery)->whereMonth('created_at', now()->month)->count(),
            'by_room' => $summaryQuery->with('room')->get()->groupBy('room.room_name')->map->count(),
        ];

        return Inertia::render('Admin/ParallelSessions/Index', [
            'filters' => $filters,
            'parallelSessions' => $parallelSessions,
            'conferences' => $conferences,
            'summary' => $summary,
        ]);
    }

    /**
     * Show the specified parallel session
     */
    public function show(ParallelSession $parallelSession): Response
    {
        $parallelSession->load(['audience.conference', 'room']);
        
        return Inertia::render('Admin/ParallelSessions/Show', [
            'parallelSession' => $parallelSession,
        ]);
    }

    /**
     * Remove the specified parallel session from storage
     */
    public function destroy(ParallelSession $parallelSession)
    {
        $parallelSession->delete();
        
        return redirect()->back()->with('success', 'Parallel session deleted successfully.');
    }
}
