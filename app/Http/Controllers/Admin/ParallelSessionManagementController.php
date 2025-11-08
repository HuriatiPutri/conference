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
        $filters = RequestFacade::only('conference_id', 'search');
        $perPage = RequestFacade::input('per_page', 15); // Default 15, bisa diubah via parameter
        $search = RequestFacade::input('search', '');
        
        // Build query with filters
        $query = ParallelSession::query()
            ->with(['audience.conference', 'room'])
            ->whereHas('audience.conference');

        // Apply search filter across multiple fields
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name_of_presenter', 'LIKE', "%{$search}%")
                  ->orWhere('paper_title', 'LIKE', "%{$search}%")
                  ->orWhereHas('audience', function ($subQ) use ($search) {
                      $subQ->whereHas('conference', function ($confQ) use ($search) {
                          $confQ->where('name', 'LIKE', "%{$search}%");
                      });
                  })
                  ->orWhereHas('room', function ($roomQ) use ($search) {
                      $roomQ->where('room_name', 'LIKE', "%{$search}%");
                  });
            });
        }

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

        return Inertia::render('Admin/ParallelSessions/Index', [
            'filters' => $filters,
            'parallelSessions' => $parallelSessions,
            'conferences' => $conferences,
            'search' => $search,
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
