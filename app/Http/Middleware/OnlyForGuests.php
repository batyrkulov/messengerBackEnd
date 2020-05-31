<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class OnlyForGuests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->cookie('api_token') && User::where('api_token', $request->cookie('api_token'))->first())
            return response()->json(['error_code'=>1, 'error'=>'access error', 'data'=>'']);
        else
            return $next($request);
    }
}
