<?php

namespace App\Http\Controllers;

use App\Models\Audience;
use App\Models\Conference;
use App\Models\KeyNote;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class KeynoteController extends Controller
{
    /**
     * Show keynote input form for conference
     */
    public function create(Conference $conference): Response
    {
        return Inertia::render('Keynote/Create', [
            'conference' => $conference->only([
                'id', 'public_id', 'name', 'initial', 'date', 'city', 'country'
            ])
        ]);
    }

    /**
     * Store keynote feedback from audience
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
            'feedback' => 'required|string|max:2000',
        ], [
            'email.exists' => 'Email not found in conference registration. Please use the email you registered with.',
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

        // Check if keynote feedback already exists for this audience
        $existingKeynote = KeyNote::where('audience_id', $audience->id)->first();

        if ($existingKeynote) {
            // Update existing keynote feedback
            $existingKeynote->update([
                'name_of_participant' => $validatedData['first_name'] . ' ' . $validatedData['last_name'],
                'feedback' => $validatedData['feedback'],
            ]);
            return redirect()->route('keynote.success', ['conference' => $conference->public_id]);
        } else {
            KeyNote::create([
                'audience_id' => $audience->id,
                'name_of_participant' => $validatedData['first_name'] . ' ' . $validatedData['last_name'],
                'feedback' => $validatedData['feedback'],
            ]);

            return redirect()->route('keynote.success', ['conference' => $conference->public_id]);
        }
    }

    /**
     * Show success page after keynote submission
     */
    public function success(Conference $conference): Response
    {
        return Inertia::render('Keynote/Success', [
            'conference' => $conference->only([
                'name', 'initial', 'date'
            ])
        ]);
    }
}
