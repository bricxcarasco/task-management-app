<?php

namespace App\Observers;

use App\Enums\ElectronicContract\ElectronicContractStatuses;
use App\Models\ElectronicContract;

class ElectronicContractObserver
{
    /**
     * Handle the ElectronicContract "creating" event.
     *
     * @param  \App\Models\ElectronicContract  $contract
     * @return void
     */
    public function creating(ElectronicContract $contract)
    {
        $contract->status = ElectronicContractStatuses::CREATED;
    }
}
