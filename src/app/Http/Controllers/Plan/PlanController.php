<?php

namespace App\Http\Controllers\Plan;

use App\Enums\PaidPlan\PlanType;
use App\Enums\ServiceSelectionTypes;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscriber;
use App\Objects\ServiceSelected;
use App\Services\StripeService;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get subject selected session
        $service = ServiceSelected::getSelected();
        $serviceName = $service->type == 'RIO' ? 'rio' : 'neo';

        $plans = Plan::serviceSelected()->get();

        return view('plans.index', compact(
            'service',
            'serviceName',
            'plans'
        ));
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Plan $plan
     *
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Plan $plan)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        // Get subject selected session
        $service = ServiceSelected::getSelected();
        $serviceName = $service->type == 'RIO' ? 'rio' : 'neo';

        $this->authorize('view', [Plan::class, $plan, $service]);

        $isDisplay = false;

        $activeSubscription = Subscriber::active();

        if ($service->type == ServiceSelectionTypes::NEO) {
            $activeSubscription = $activeSubscription->where('neo_id', $service->data->id)->first();
        } else {
            $activeSubscription = $activeSubscription->where('rio_id', $user->rio_id)->first();
        }

        if (!in_array($plan->id, [PlanType::RIO_FREE_PLAN, PlanType::NEO_FREE_PLAN]) && !$activeSubscription) {
            $incompleteSubscription = Subscriber::incompletePayment();

            if ($service->type == ServiceSelectionTypes::NEO) {
                $incompleteSubscription = $incompleteSubscription->where('neo_id', $service->data->id)->first();
            } else {
                $incompleteSubscription = $incompleteSubscription->where('rio_id', $user->rio_id)->first();
            }

            $subscriptionService = new StripeService();

            if ($incompleteSubscription && $incompleteSubscription->plan_id != $plan->id) {
                $subscriptionService->cancelPaymentIntent($incompleteSubscription);
            }

            $subscriptionInstance = $subscriptionService->getCustomerInfo($plan);

            $isDisplay = true;
        }

        $subscription = $subscriptionInstance['subscriber'] ?? [];
        $subscriptionExists = $subscriptionInstance['exists'] ?? [];

        return view('plans.show', compact(
            'service',
            'serviceName',
            'plan',
            'subscription',
            'subscriptionExists',
            'isDisplay'
        ));
    }
}
