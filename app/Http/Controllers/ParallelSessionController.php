<?php

namespace App\Http\Controllers;

use App\Models\Audience;
use App\Models\Conference;
use App\Models\ParallelSession;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ParallelSessionController extends Controller
{
    /**
     * Show parallel session input form for conference
     */
    public function create(Conference $conference): Response
    {
        $rooms = Room::where('conference_id', $conference->id)
                    // ->whereNull('deleted_at')
                    ->orderBy('room_name', 'asc')
                    ->get(['id', 'room_name']);

        return Inertia::render('ParallelSession/Create', [
            'conference' => $conference->only([
                'id', 'public_id', 'name', 'initial', 'date', 'city', 'country'
            ]),
            'rooms' => $rooms
        ]);
    }

    /**
     * Store parallel session info from audience
     */
    public function store(Request $request, Conference $conference)
    {
        $validatedData = $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::exists('audiences')->where(function ($query) use ($conference) {
                    return $query->where('conference_id', $conference->id)
                                 ->whereNull('deleted_at');
                }),
            ],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'room_id' => [
                'required',
                'integer',
                Rule::exists('rooms', 'id')->where(function ($query) use ($conference) {
                    return $query->where('conference_id', $conference->id);
                                //  ->whereNull('deleted_at');
                }),
            ],
            'paper_title' => 'required|string|max:500',
        ], [
            'email.exists' => 'Email not found in conference registration. Please use the email you registered with.',
            'room_id.exists' => 'Selected room is not available for this conference.',
        ]);

        // Find the audience record
        $audience = Audience::where('email', $validatedData['email'])
                           ->where('conference_id', $conference->id)
                           ->whereNull('deleted_at')
                           ->first();

        if (!$audience) {
            return redirect()->back()->withErrors([
                'email' => 'Email not found in conference registration.'
            ]);
        }

        // Check if parallel session already exists for this audience
        $existingSession = ParallelSession::where('audience_id', $audience->id)->first();

        if ($existingSession) {
            // Update existing parallel session
            $existingSession->update([
                'name_of_presenter' => $validatedData['first_name'] . ' ' . $validatedData['last_name'],
                'room_id' => $validatedData['room_id'],
                'paper_title' => $validatedData['paper_title'],
            ]);
            return redirect()->route('parallel-session.success', ['conference' => $conference->public_id]);
        } else {
            // Create new parallel session
            ParallelSession::create([
                'audience_id' => $audience->id,
                'name_of_presenter' => $validatedData['first_name'] . ' ' . $validatedData['last_name'],
                'room_id' => $validatedData['room_id'],
                'paper_title' => $validatedData['paper_title'],
            ]);
            return redirect()->route('parallel-session.success', ['conference' => $conference->public_id]);
        }
    }

    /**
     * Show success page after parallel session submission
     */
    public function success(Conference $conference): Response
    {
        return Inertia::render('ParallelSession/Success', [
            'conference' => $conference->only([
                'name', 'initial', 'date'
            ])
        ]);
    }
}
