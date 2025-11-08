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
        $filters = RequestFacade::only('conference_id', 'search');
        $perPage = RequestFacade::input('per_page', 15); // Default 15, bisa diubah via parameter

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

        // Apply search filter
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function($q) use ($searchTerm) {
                $q->where('name_of_participant', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('feedback', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('audience', function($audienceQuery) use ($searchTerm) {
                      $audienceQuery->where('email', 'LIKE', "%{$searchTerm}%")
                                   ->orWhere('first_name', 'LIKE', "%{$searchTerm}%")
                                   ->orWhere('last_name', 'LIKE', "%{$searchTerm}%");
                  })
                  ->orWhereHas('audience.conference', function($confQuery) use ($searchTerm) {
                      $confQuery->where('name', 'LIKE', "%{$searchTerm}%")
                               ->orWhere('initial', 'LIKE', "%{$searchTerm}%");
                  });
            });
        }

        // Get keynotes with pagination
        $keynotes = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends(RequestFacade::all());

        // Get all conferences for filter dropdown
        $conferences = Conference::whereNull('deleted_at')
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Admin/Keynotes/Index', [
            'filters' => $filters,
            'keynotes' => $keynotes,
            'conferences' => $conferences,
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
