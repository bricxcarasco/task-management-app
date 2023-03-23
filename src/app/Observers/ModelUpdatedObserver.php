<?php

namespace App\Observers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ModelUpdatedObserver
{
    /**
     * when model instance is creating.
     * @return void
     */
    public function creating(Model $model)
    {
        // @phpstan-ignore-next-line
        $model->updated_at = Carbon::now();
    }

    /**
     * when model instance is updating.
     * @return void
     */
    public function updating(Model $model)
    {
        // @phpstan-ignore-next-line
        $model->updated_at = Carbon::now();
    }

    /**
     * when model instance is saving.
     * @return void
     */
    public function saving(Model $model)
    {
        // @phpstan-ignore-next-line
        $model->updated_at = Carbon::now();
    }
}
