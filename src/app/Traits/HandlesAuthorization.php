<?php

namespace App\Traits;

use Illuminate\Auth\Access\Response;

trait HandlesAuthorization
{
    /**
     * Create a new access response.
     *
     * @param  string|null  $message
     * @param  mixed  $code
     * @return \Illuminate\Auth\Access\Response
     */
    protected function allow($message = null, $code = null)
    {
        return Response::allow($message, $code);
    }

    /**
     * Throws an unauthorized exception.
     *
     * @param  bool  $isForbidden Boolean whether to display forbidden error or not found
     * @return \Illuminate\Auth\Access\Response
     */
    protected function deny($isForbidden = false)
    {
        if (!$isForbidden) {
            /** @phpstan-ignore-next-line */
            abort(404, __('Not Found'));
        }

        return Response::deny();
    }
}
