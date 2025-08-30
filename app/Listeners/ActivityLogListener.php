<?php

namespace App\Listeners;

use App\Events\ActivityLogEvent;
use App\Models\ActivityLog;

class ActivityLogListener
{
    public function handle(ActivityLogEvent $event): void
    {
        ActivityLog::create([
            'level' => $event->level,
            'message' => $event->message,
            'context' => $event->context,
        ]);
    }
}
