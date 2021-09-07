<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Client;
use App\Models\Manager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;

class IsAdminOrClient
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
        $userId = Auth::user()->id;

        if (Client::where('user_id', $userId)->exists()) {
            return $next($request);
        }

        if (Manager::where('user_id', $userId)->exists()) {
            return $next($request);
        }

        return redirect(RouteServiceProvider::HOME);
    }
}
