<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        $activityLogs = ActivityLog::all(); // Mengambil semua data

        return view('home.activity-log.index', compact('activityLogs'));
    }

    public function store(Request $request)
    {
        $log = ActivityLog::create($request->all());

        return response()->json($log, 201);
    }

    public function show(ActivityLog $activityLog)
    {
        return view('home.activity-log.show', compact('activityLog'));
    }
}
