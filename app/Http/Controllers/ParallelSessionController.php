<?php

namespace App\Http\Controllers;

use App\Models\Audience;
use App\Models\Conference;
use App\Models\ParallelSession;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
            'first_name' => 'required|string|max:255|regex:/^[a-zA-Z\s\'\.\-]+$/',
            'last_name' => 'required|string|max:255|regex:/^[a-zA-Z\s\'\.\-]+$/',
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
            'first_name.regex' => 'First name can only contain letters, spaces, apostrophes, dots, and hyphens.',
            'last_name.regex' => 'Last name can only contain letters, spaces, apostrophes, dots, and hyphens.',
        ]);

        // Sanitize text inputs to handle special characters safely
        $validatedData['first_name'] = trim($validatedData['first_name']);
        $validatedData['last_name'] = trim($validatedData['last_name']);
        $validatedData['paper_title'] = trim($validatedData['paper_title']);

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

        try {
            if ($existingSession) {
                // Update existing parallel session
                return redirect()->back()->withErrors([
                    'error' => 'You have already submitted a parallel session.'
                ]);
            } else {
                // Create new parallel session
                ParallelSession::create([
                    'audience_id' => $audience->id,
                    'name_of_presenter' => $validatedData['first_name'] . ' ' . $validatedData['last_name'],
                    'room_id' => $validatedData['room_id'],
                    'paper_title' => $validatedData['paper_title'],
                ]);
            }
            
            return redirect()->route('parallel-session.success', ['conference' => $conference->public_id]);
        } catch (\Exception $e) {
            Log::error('Parallel Session creation/update failed: ' . $e->getMessage());
            return redirect()->back()->withErrors([
                'error' => 'An error occurred while saving your submission. Please try again.'
            ])->withInput();
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
