<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanitizeInput
{
    public function handle(Request $request, Closure $next)
    {
        // Sanitize input untuk extra protection
        $input = $request->all();
        
        array_walk_recursive($input, function (&$input) {
            if (is_string($input)) {
                // Remove potential XSS characters
                $input = strip_tags($input);
                // Remove potential SQL injection patterns
                $input = str_replace(['--', ';', '/*', '*/', 'xp_', 'sp_'], '', $input);
            }
        });
        
        $request->merge($input);
        
        return $next($request);
    }
}