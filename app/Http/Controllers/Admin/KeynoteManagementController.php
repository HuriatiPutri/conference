<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KeyNote;
use App\Models\Conference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as RequestFacade;
use Inertia\Inertia;
use Inertia\Response;

class KeynoteManagementController extends Controller
{
    /**
     * Display a listing of keynotes
     */
    public function index(): Response
    {
        $filters = RequestFacade::only('conference_id');
        
        // Build query with filters
        $query = KeyNote::query()
            ->with(['audience.conference'])
            ->whereHas('audience.conference');

        // Apply conference filter
        if (!empty($filters['conference_id'])) {
            $query->whereHas('audience', function ($q) use ($filters) {
                $q->where('conference_id', $filters['conference_id']);
            });
        }

        // Get keynotes with pagination
        $keynotes = $query->orderBy('created_at', 'desc')->paginate(15)->appends(RequestFacade::all());

        // Get all conferences for filter dropdown
        $conferences = Conference::whereNull('deleted_at')
            ->orderBy('name')
            ->get(['id', 'name']);

        // Get summary counts
        $summaryQuery = KeyNote::query()->whereHas('audience.conference');
        
        if (!empty($filters['conference_id'])) {
            $summaryQuery->whereHas('audience', function ($q) use ($filters) {
                $q->where('conference_id', $filters['conference_id']);
            });
        }

        $summary = [
            'total' => $summaryQuery->count(),
            'this_month' => (clone $summaryQuery)->whereMonth('created_at', now()->month)->count(),
        ];

        return Inertia::render('Admin/Keynotes/Index', [
            'filters' => $filters,
            'keynotes' => $keynotes,
            'conferences' => $conferences,
            'summary' => $summary,
        ]);
    }

    /**
     * Show the specified keynote
     */
    public function show(KeyNote $keynote): Response
    {
        $keynote->load(['audience.conference']);
        
        return Inertia::render('Admin/Keynotes/Show', [
            'keynote' => $keynote,
        ]);
    }

    /**
     * Remove the specified keynote from storage
     */
    public function destroy(KeyNote $keynote)
    {
        $keynote->delete();
        
        return redirect()->back()->with('success', 'Keynote feedback deleted successfully.');
    }
}
