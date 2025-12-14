<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoaVolume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as RequestFacade;
use Inertia\Inertia;
use Inertia\Response;

class LoaVolumeManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $filters = RequestFacade::only('search');
        $perPage = RequestFacade::input('per_page', 15);
        $search = RequestFacade::input('search', '');
        
        // Build query with filters and audience count (including JOIV registrations)
        $query = LoaVolume::query()
            ->with(['creator', 'updater'])
            ->withCount([
                'audiences',
                'joivRegistrations'
            ]);

        // Apply search filter
        if (!empty($search)) {
            $query->where('volume', 'LIKE', "%{$search}%");
        }

        // Get loa volumes with pagination
        $loaVolumes = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends(RequestFacade::all());

        // Get summary counts
        $summaryQuery = LoaVolume::query();
        
        if (!empty($search)) {
            $summaryQuery->where('volume', 'LIKE', "%{$search}%");
        }

        $summary = [
            'total' => $summaryQuery->count(),
            'this_month' => (clone $summaryQuery)->whereMonth('created_at', now()->month)->count(),
        ];

        return Inertia::render('Admin/LoaVolumes/Index', [
            'filters' => $filters,
            'loaVolumes' => $loaVolumes,
            'summary' => $summary,
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/LoaVolumes/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $volume = trim($request->volume);
        
        // Check for case-insensitive duplicate
        $existingVolume = LoaVolume::whereRaw('LOWER(volume) = ?', [strtolower($volume)])->first();
        if ($existingVolume) {
            return back()->withErrors([
                'volume' => 'This volume already exists (case-insensitive). Please choose a different volume name.'
            ])->withInput();
        }

        $request->validate([
            'volume' => 'required|string|max:255',
        ], [
            'volume.required' => 'Volume is required.',
            'volume.string' => 'Volume must be a string.',
            'volume.max' => 'Volume may not be greater than 255 characters.',
        ]);

        LoaVolume::create([
            'volume' => $volume,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('loa.loa-volumes.index')->with('success', 'LoA Volume created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(LoaVolume $loaVolume): Response
    {
        $loaVolume->load([
            'creator',
            'updater',
            'audiences' => function($query) {
                $query->with(['conference'])
                      ->where('payment_status', 'paid')
                      ->whereNotNull('paper_title')
                      ->select('id', 'first_name', 'last_name', 'paper_title', 'loa_authors', 'institution', 'conference_id', 'loa_volume_id', 'full_paper_path')
                      ->orderBy('first_name');
            },
            'joivRegistrations' => function($query) {
                $query->where('payment_status', 'paid')
                      ->whereNotNull('paper_title')
                      ->select('id', 'first_name', 'last_name', 'paper_title', 'loa_authors', 'institution', 'loa_volume_id', 'full_paper_path')
                      ->orderBy('first_name');
            }
        ]);
        
        return Inertia::render('Admin/LoaVolumes/Show', [
            'loaVolume' => $loaVolume,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LoaVolume $loaVolume): Response
    {
        return Inertia::render('Admin/LoaVolumes/Edit', [
            'loaVolume' => $loaVolume,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LoaVolume $loaVolume)
    {
        $volume = trim($request->volume);
        
        // Check for case-insensitive duplicate (excluding current record)
        $existingVolume = LoaVolume::whereRaw('LOWER(volume) = ?', [strtolower($volume)])
                                   ->where('id', '!=', $loaVolume->id)
                                   ->first();
        if ($existingVolume) {
            return back()->withErrors([
                'volume' => 'This volume already exists (case-insensitive). Please choose a different volume name.'
            ])->withInput();
        }

        $request->validate([
            'volume' => 'required|string|max:255',
        ], [
            'volume.required' => 'Volume is required.',
            'volume.string' => 'Volume must be a string.',
            'volume.max' => 'Volume may not be greater than 255 characters.',
        ]);

        $loaVolume->update([
            'volume' => $volume,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('loa.loa-volumes.index')->with('success', 'LoA Volume updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LoaVolume $loaVolume)
    {
        $loaVolume->delete();
        
        return redirect()->back()->with('success', 'LoA Volume deleted successfully.');
    }
}
