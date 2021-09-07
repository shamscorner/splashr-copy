<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Manager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Manager::where('user_id', Auth::user()->id)->exists()) {
            return redirect(RouteServiceProvider::HOME);
        }

        return $next($request);
    }
}
