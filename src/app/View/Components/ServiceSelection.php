<?php

namespace App\View\Components;

use App\Enums\NeoBelongStatuses;
use Illuminate\View\Component;
use App\Models\User;
use Session;

class ServiceSelection extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        /** @var User */
        $user = auth()->user();

        return view(
            'components.service-selection',
            [
                'currentUser' => $this->user(),
                'neos' => $user->rio->neos()
                    ->whereStatus(NeoBelongStatuses::AFFILIATED)
                    ->paginate(config('bphero.paginate_count')),
                'serviceSelected' => $this->serviceSelected(),
            ]
        );
    }

    /**
     * Get autheticated user.
     *
     * @return mixed
     */
    public function user()
    {
        /** @var User */
        $user = auth()->user();

        return $user->rio;
    }

    /**
     * Get selected service.
     *
     * @return mixed
     */
    public function serviceSelected()
    {
        $serviceSession = json_decode(Session::get('ServiceSelected'));

        return $serviceSession;
    }
}
