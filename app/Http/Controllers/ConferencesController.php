<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConferenceStoreRequest;
use App\Http\Requests\ConferenceTemplateSettingRequest;
use App\Http\Resources\ConferenceCollection;
use App\Http\Resources\ConferenceResource;
use App\Models\Conference;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;
use Inertia\Response;

class ConferencesController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Conferences/Index', [
            'filters' => Request::all('search', 'trashed'),
            'conferences' => new ConferenceCollection(
                Conference::query()
                ->orderBy('id', 'desc')
                ->filter(Request::only('search', 'trashed'))
                ->paginate()
                ->appends(Request::all())
            ),
        ]);
    }

    public function show(Conference $conference): Response
    {
        return Inertia::render('Admin/Conferences/Show', [
            'conference' => new ConferenceResource($conference),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Conferences/Create');
    }

    public function store(ConferenceStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['public_id'] = uniqid('');

        if($request->hasFile('cover_poster_path')) {
          $validated['cover_poster_path'] = $request->file('cover_poster_path')->store('conference_posters', 'public');
        }
        
        $conference = Conference::create(collect($validated)->except('rooms')->toArray());

        $room = [];
        foreach ($validated['rooms'] as $roomData) {
            $room['conference_id'] = $conference->id;
            $room['room_name'] = $roomData['room_name'];
            $conference->rooms()->create($room);
        }

        if ($conference->rooms()->count() == 0) {
            return redirect()->back()->withErrors(['rooms' => 'At least one room must be added.'])->withInput();
        }

        return Redirect::route('conferences')->with('success', 'Conference created.');
    }

    public function edit(Conference $conference): Response
    {
        return Inertia::render('Admin/Conferences/Edit', [
            'conference' => new ConferenceResource($conference),
        ]);
    }

    public function update(Conference $conference, ConferenceStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if($request->hasFile('cover_poster_path')) {
          $validated['cover_poster_path'] = $request->file('cover_poster_path')->store('conference_posters', 'public');
        } else {
          // Jangan update cover_poster_path jika tidak ada file baru
          unset($validated['cover_poster_path']);
        }
        $conference->update(collect($validated)->except('rooms')->toArray());

        $existingRoomIds = $conference->rooms()->pluck('id')->toArray();
        $keptIds = [];

        foreach ($validated['rooms'] as $roomData) {
            // Normalize nama field
            $incomingName = $roomData['room_name'] ?? $roomData['name'] ?? null;
            if (!$incomingName) {
                continue; // skip jika kosong
            }

            if (!empty($roomData['id']) && in_array($roomData['id'], $existingRoomIds)) {
                // Update existing
                $room = $conference->rooms()->where('id', $roomData['id'])->first();
                $room->update(['room_name' => $incomingName]);
                $keptIds[] = $roomData['id'];
            } else {
                // Create new
                $newRoom = $conference->rooms()->create([
                    'conference_id' => $conference->id,
                    'room_name' => $incomingName,
                ]);
                $keptIds[] = $newRoom->id;
            }
        }

        // Hapus room yang tidak lagi dikirim
        $toDelete = array_diff($existingRoomIds, $keptIds);
        if (!empty($toDelete)) {
            $conference->rooms()->whereIn('id', $toDelete)->delete();
        }

        return Redirect::route('conferences')->with('success', 'Conference updated.');
    }

    public function setting(Conference $conference): Response
    {
        return Inertia::render('Admin/Conferences/Setting', [
            'conference' => new ConferenceResource($conference),
            'user' => Auth::user(),
        ]);
    }

    public function uploadCertificate(Conference $conference, ConferenceTemplateSettingRequest $request): RedirectResponse
    {
        $updateData = [];
        
        // Handle file upload
        if($request->hasFile('certificate_template_path')) {
            $storedPath = $request->file('certificate_template_path')->store('conference_certificate', 'public');
            $updateData['certificate_template_path'] = $storedPath;
        }
        // Handle position data
        if($request->has('certificate_template_position')) {
            $updateData['certificate_template_position'] = $request->input('certificate_template_position');
        }
        
        if(empty($updateData)) {
            return Redirect::back()->withErrors(['error' => 'Tidak ada data yang diupdate.']);
        }
        
        $conference->update($updateData);
        
        return Redirect::back()->with('success', 'Template sertifikat berhasil diperbarui.');
    }

    public function updateCertificatePosition(Conference $conference, ConferenceTemplateSettingRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        
        // Update hanya posisi koordinat
        $conference->update([
            'certificate_participant_name_x' => $validated['certificate_participant_name_x'],
            'certificate_participant_name_y' => $validated['certificate_participant_name_y'],
            'certificate_paper_title_x' => $validated['certificate_paper_title_x'],
            'certificate_paper_title_y' => $validated['certificate_paper_title_y'],
        ]);
        
        return Redirect::back()->with('success', 'Posisi teks sertifikat berhasil diperbarui.');
    }

    public function destroy(Conference $conference): RedirectResponse
    {
        $conference->delete();

        return Redirect::back()->with('success', 'Conference deleted.');
    }

    public function restore(Conference $conference): RedirectResponse
    {
        $conference->restore();

        return Redirect::back()->with('success', 'Conference restored.');
    }
}
