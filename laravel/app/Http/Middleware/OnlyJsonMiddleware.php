<?php

namespace App\Http\Middleware;

use Closure;

class OnlyJsonMiddleware
{
    /**
     * We only accept json
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->isMethod('post')) return $next($request);

        $acceptHeader = $request->header('Content-Type');
        if ($acceptHeader != 'application/json') {
            $msg = ['Description' => 'Specified content type not allowed.'];
            return response()->json($msg, 415, $msg);
        }

        return $next($request);
    }
}
