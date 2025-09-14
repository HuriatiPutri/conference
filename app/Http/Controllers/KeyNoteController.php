<?php

namespace App\Http\Controllers;

use App\Models\Audience;
use App\Models\Conference;
use App\Models\KeyNote;
use Illuminate\Http\Request;

class KeyNoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Conference $conference)
    {
        return view('keynote/index', [
            'conference' => $conference,
        ]);
    }

    public function keynoteList()
    {
        $keynotes = KeyNote::with('conference')->get();
        $conferences = Conference::all();

        return view('home/keynote/index', [
            'keynotes' => $keynotes,
            'conferences' => $conferences,
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $audience = Audience::where('email', $request->email)
            ->where('conference_id', $request->conference_id)
            // ->where('payment_status', 'paid')
            ->first();
        if (!$audience) {
            return redirect()->back()->withInput()->withErrors(['email' => 'This email is not registered for the conference.']);
        } else {
            $isKeynoteExist = KeyNote::where('audience_id', $audience->id)->first();
            if ($isKeynoteExist) {
                return redirect()->back()->withInput()->withErrors(['email' => 'The keynote has been successfully submitted for this conference']);
            }
            $request['name_of_participant'] = $request->first_name.' '.$request->last_name;
            $request['audience_id'] = $audience->id;

            $validatedData = $request->validate([
                'audience_id' => 'required|exists:audiences,id',
                'name_of_participant' => 'required|string|max:255',
                'feedback' => 'required|string|max:1000',
            ]);

            $keynote = KeyNote::create($validatedData);

            return redirect()->route('keynote.show', $keynote->id)->with('success', 'Your submission was successful!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(KeyNote $keyNote)
    {
        return view('keynote.detail', [
            'keynote' => $keyNote,
        ]);
    }
}
