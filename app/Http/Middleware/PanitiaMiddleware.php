<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PanitiaMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('panitia')->check()) {
            return redirect('/login');
        }
        return $next($request);
    }
}

