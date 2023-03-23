<?php

namespace App\Http\Middleware;

use App\Services\CordovaService;
use Closure;
use Illuminate\Http\Request;

class VerifyPlatformToken
{
    /**
     * Verifies platform cookie
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (CordovaService::hasCookie()) {
            CordovaService::verifyToken();
        }

        return $next($request);
    }
}
