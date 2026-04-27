<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conference;
use App\Models\Audience;
use App\Models\Membership;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = Auth::user();

        $statRoleUser = [
            'memberships' => Membership::with('package')->where('user_id', $user->id)->latest()->first(),
            'recent_conferences' => Audience::with('conference:id,name,city,date,year')->where('user_id', $user->id)->orWhere('email', $user->email)->latest()->take(5)->get(['id', 'first_name', 'last_name', 'email', 'conference_id', 'created_at', 'payment_status']),
            'upcoming_conference' => Conference::where('date', '>=', Carbon::today())->orderBy('date', 'asc')->take(3)->get(),
        ];

        $stats = [
            'total_conferences' => Conference::count(),
            'total_audiences' => Audience::whereHas('conference')->count(),
            'recent_conferences' => Conference::latest()->take(5)->get(['id', 'name', 'initial', 'date', 'city']),
            'recent_audiences' => Audience::whereHas('conference')->with('conference:id,name')->latest()->take(5)->get(['id', 'first_name', 'last_name', 'email', 'conference_id', 'created_at']),
        ];
        if ($user->hasRole('user')) {
            $view = 'Admin/Dashboard/User';
        } else {
            $view = 'Admin/Dashboard/Admin';
        }
        return Inertia::render($view, [
            'stats' => $stats,
            'statRoleUser' => $statRoleUser,
            'user' => Auth::user(),
            'packages' => \App\Models\Package::active()->get(),
        ]);
    }

    public function membershipCard(): Response
    {
        $user = Auth::user();

        $membership = Membership::with('package')
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        return Inertia::render('Admin/Memberships/Card', [
            'membership' => $membership,
            'user' => $user,
        ]);
    }
}
