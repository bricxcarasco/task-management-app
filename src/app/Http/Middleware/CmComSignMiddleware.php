<?php

namespace App\Http\Middleware;

use App\Services\CmCom\SignService;
use Closure;
use Illuminate\Http\Request;

class CmComSignMiddleware
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
        /** @var string $signature */
        $signature = $request->header('cm-sign-webhook-key');

        if (!SignService::isValidWebhookSignature($signature)) {
            return response()->respondUnauthorized();
        }

        return $next($request);
    }
}
