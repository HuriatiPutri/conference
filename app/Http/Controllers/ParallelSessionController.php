<?php

namespace App\Http\Controllers;

use App\Models\Audience;
use App\Models\Conference;
use App\Models\ParallelSession;
use App\Models\Room;
use Illuminate\Http\Request;

class ParallelSessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Conference $conference)
    {
        $rooms = Room::where('conference_id', $conference->id)->get();

        return view('parallel_session/index', [
            'conference' => $conference,
            'rooms' => $rooms,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $audience = Audience::where('email', $request->email)
        ->where('conference_id', $request->conference_id)
        ->where('payment_status', 'paid')
        ->first();
        if (!$audience) {
            return redirect()->back()->withInput()->withErrors(['email' => 'This email is not registered for the conference.']);
        } else {
            $isParallelSessionExist = ParallelSession::where('audience_id', $audience->id)->first();
            if ($isParallelSessionExist) {
                return redirect()->back()->withErrors(['email' => 'The parallel session has been successfully submitted for this conference.']);
            }
            $request['name_of_presenter'] = $request->first_name.' '.$request->last_name;
            $request['audience_id'] = $audience->id;

            $validatedData = $request->validate([
                'conference_id' => 'required|exists:conferences,id',
                'audience_id' => 'required|exists:audiences,id',
                'name_of_presenter' => 'required|string|max:225',
                'room_id' => 'required|exists:rooms,id',
                'paper_title' => 'required|string|max:225',
            ]);

            $parallelSession = ParallelSession::create($validatedData);

            return redirect()->route('parallel-session.show', $parallelSession->id)->with('success', 'Your submission was successful!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ParallelSession $parallelSession)
    {
        return view('parallel_session.detail', [
            'parallelSession' => $parallelSession,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ParallelSession $parallelSession)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ParallelSession $parallelSession)
    {
    }
}
