<?php

namespace App\Http\Controllers;

use App\Models\Conference;
use App\Models\Audience;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $stats = [
            'total_conferences' => Conference::count(),
            'total_audiences' => Audience::whereHas('conference')->count(),
            'recent_conferences' => Conference::latest()->take(5)->get(['id', 'name', 'initial', 'date', 'city']),
            'recent_audiences' => Audience::whereHas('conference')->with('conference:id,name')->latest()->take(5)->get(['id', 'first_name', 'last_name', 'email', 'conference_id', 'created_at']),
        ];

        return Inertia::render('Dashboard/Index', [
            'stats' => $stats,
            'user' => Auth::user(),
        ]);
    }
}
