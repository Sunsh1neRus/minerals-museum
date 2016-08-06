<?php

namespace App\Http\Middleware;

use Closure;

class AdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (is_null($request->user()) OR !$request->user()->isAdmin()) {
            return redirect('/')->withErrors(['У вас нет прав доступа к этой странице'])->setStatusCode(403);
        }

        return $next($request);
    }
}
