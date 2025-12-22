<?php
// app/Http/Middleware/CheckMeetingLimit.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckMeetingLimit
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->user()->canUploadMeetings()) {
            return back()->with('error', 'Has alcanzado el límite de reuniones para tu plan. Actualiza tu plan para continuar.');
        }

        return $next($request);
    }
}