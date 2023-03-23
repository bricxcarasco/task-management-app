<?php

namespace App\Traits;

use App\Observers\ModelUpdatedObserver;

trait ModelUpdatedTrait
{
    /**
     * observe model for changes
     *
     * @return void
     */
    public static function bootModelUpdatedObservable()
    {
        self::observe(ModelUpdatedObserver::class);
    }
}