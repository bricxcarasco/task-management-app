<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Neo;
use App\Models\NeoBelong;

class NeoOwnerMiddleware
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

        /** @var Neo */
        $neo = $request->route('neo');

        /** @var NeoBelong */
        $neo_owner = $neo->owner;

        if ($user->rio_id !== $neo_owner->rio_id) {
            /** @phpstan-ignore-next-line */
            abort(404, __('Not Found'));
        }

        return $next($request);
    }
}
