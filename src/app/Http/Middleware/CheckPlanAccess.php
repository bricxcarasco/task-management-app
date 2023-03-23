<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\ElectronicContract;
use App\Objects\ServiceSelected;
use Illuminate\Http\Request;

class CheckPlanAccess
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
        // Get current service
        $currentService = ServiceSelected::getSelected();

        $availableSlot = ElectronicContract::availableSlot($currentService);
        ['slot' => $slot] = $availableSlot;
        ['expired' => $isExpired] = $availableSlot;

        // Check if route is accessible
        if ($request->routeIs((array)$currentService->plan_subscriptions->routes)) {
            return $next($request);
        }

        // Check if electronic contract is accessible
        $hasFreeSlots = !empty($slot);
        $isElectronicContracts = $request->routeIs('electronic-contracts.*');

        if (!$isExpired && $hasFreeSlots && $isElectronicContracts) {
            return $next($request);
        }

        abort(403, 'Access denied');
    }
}
