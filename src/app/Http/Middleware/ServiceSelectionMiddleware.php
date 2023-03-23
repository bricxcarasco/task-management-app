<?php

namespace App\Http\Middleware;

use App\Enums\ServiceSelectionTypes;
use App\Models\User;
use App\Objects\ServiceSelected;
use Closure;
use Illuminate\Http\Request;

class ServiceSelectionMiddleware
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
        /** @var User */
        $user = auth()->user();

        // Get current service
        $currentService = ServiceSelected::getSelected();

        // When expired, get information from cookie
        if (!$request->session()->has('ServiceSelected')) {
            $currentService = ServiceSelected::getCookie();
        }

        // Refetch NEO user data and refresh session
        if (!empty($currentService) && $currentService->type === ServiceSelectionTypes::NEO) {
            $neoBelong =  $user
                ->rio
                ->neos
                ->where('id', $currentService->data->id)
                ->first();

            if (!empty($neoBelong)) {
                ServiceSelected::setSelected($neoBelong);

                return $next($request);
            }
        }

        // Refresh RIO user data session
        ServiceSelected::setSelected($user->rio);

        return $next($request);
    }
}
