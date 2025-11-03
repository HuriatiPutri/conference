<?php

namespace App\Http\Controllers;

use App\Models\Conference;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LandingController extends Controller
{
    public function index(): Response
    {
        // Get active conferences (currently open for registration)
        $conferences = Conference::whereNull('deleted_at')
            ->where('registration_start_date', '<=', now())
            ->where('registration_end_date', '>=', now())
            ->orderBy('date', 'asc')
            ->limit(6)
            ->get();

        // Get all conferences for the conference listing
        $allConferences = Conference::whereNull('deleted_at')
            ->orderBy('date', 'desc')
            ->limit(12)
            ->get();
        return Inertia::render('Landing/Index', [
            'activeConferences' => $conferences,
            'allConferences' => $allConferences,
        ]);
    }

    public function detail(Conference $conference): Response
    {
        return Inertia::render('Landing/Detail', [
            'conference' => $conference,
        ]);
    }
}
