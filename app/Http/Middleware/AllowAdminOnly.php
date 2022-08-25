<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AllowAdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(\Auth::user()->role != 0) {
            return abort(404, 'Page not found.');
        }
        return $next($request);
    }
}
