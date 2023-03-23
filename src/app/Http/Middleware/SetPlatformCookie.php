<?php

namespace App\Http\Middleware;

use App\Enums\DevicePlatforms;
use App\Services\CordovaService;
use Closure;
use Illuminate\Http\Request;

class SetPlatformCookie
{
    /**
     * Set platform cookie when platform_device was passed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var string|null */
        $device = $request->query('platform_device');

        // Guard clause for empty cookie
        if (empty($device)) {
            return $next($request);
        }

        // Set cookie if platform exists
        if (DevicePlatforms::hasValue($device)) {
            CordovaService::setCookie($device);
        }

        return $next($request);
    }
}
