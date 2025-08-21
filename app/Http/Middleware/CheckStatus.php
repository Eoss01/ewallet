<?php

namespace App\Http\Middleware;

use Closure;
use App\Enums\ActiveStatus;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (Auth::check() && Auth::user()->status === ActiveStatus::Inactive)
        {
            Auth::logout();
            return redirect('login')->with('error', __('Your account is inactive.'));
        }

        return $response;
    }
}
