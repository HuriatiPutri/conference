<?php

namespace App\Events;

class ActivityLogEvent
{
    public string $level;
    public string $message;
    public array $context;

    public function __construct(string $level, string $message, array $context = [])
    {
        $this->level = $level;
        $this->message = $message;
        $this->context = $context;
    }
}
