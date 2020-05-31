<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class CorrectingData
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
        if ($request->route('page')) {
            $request->route()->setParameter('page', intval(abs($request->route('page'))));
            if ($request->route('page')<1)
                $request->route()->setParameter('page', 1);
        }
        if ($request->route('pageSize')) {
            $request->route()->setParameter('pageSize', intval(abs($request->route('pageSize'))));
            if ($request->route('pageSize')<1)
                $request->route()->setParameter('pageSize', 1);
        }
        if ($request->route('userId')) {
            $request->route()->setParameter('userId', intval(abs($request->route('userId'))));
        }

        return $next($request);
    }
}
