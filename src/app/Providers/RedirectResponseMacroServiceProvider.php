<?php

namespace App\Providers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\ServiceProvider;
use Throwable;

class RedirectResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        /**
         * RedirectResponse Macro - withAlertBox
         *
         * @param \Throwable|string $status - Use exception information or alert box type "danger" or "success"
         * @param string|null $message - message to display or add
         * @param array $data - other attachments
         *
         * @return this
         */
        RedirectResponse::macro('withAlertBox', function ($status, $message = '', $data = []) {
            // If exception is passed
            if ($status instanceof Throwable) {
                // Prepare variabled based on exception received
                $exception = $status;
                $errorMessage = $exception->getMessage();

                // Overwrite message
                if (!empty($message)) {
                    $errorMessage = $message;
                }

                // Initialize to danger status
                $status = 'danger';
            }

            // Priority alert data
            $baseData = compact(
                'status',
                'message'
            );

            // Merge with other data
            $alertData = array_merge($data, $baseData);

            /* @phpstan-ignore-next-line */
            return $this->with([
                'alert' => $alertData,
            ]);
        });
    }
}
