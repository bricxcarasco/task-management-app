<?php

namespace App\Services;

use App\Enums\PlanServiceType;
use App\Helpers\CommonHelper;
use App\Models\Document;
use App\Models\Plan;
use App\Models\PlanService;
use App\Models\Service;
use App\Models\ServiceSetting;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;

class PlanSubscriptionService
{
    /**
     * Get current plan
     *
     * @param \App\Models\Rio|\App\Models\Neo $entity
     * @return \App\Models\Plan|null
     */
    public static function getCurrentPlan($entity)
    {
        // Identify entity type
        $entityType = CommonHelper::getEntityType($entity);

        // Get subscriber details
        $subscriber = $entity->subscriber()
            ->active()
            ->unexpired()
            ->first();

        // Get current plan of entity
        return $subscriber->plan ?? self::getFreePlan($entityType);
    }

    /**
     * Get user accessible routes
     *
     * @param \App\Models\Rio|\App\Models\Neo $entity
     * @return array
     */
    public static function getAccessibleRoutes($entity)
    {
        $plan = self::getCurrentPlan($entity);

        // Get plan accessible routes
        return $plan->accessible_routes ?? [];
    }

    /**
     * Document management access
     *
     * @param int $entityType
     * @return \App\Models\Plan|null
     */
    public static function getFreePlan($entityType)
    {
        return Plan::free()
            ->whereEntityType($entityType)
            ->first();
    }

    /**
     * Generate session data
     *
     * @param \App\Models\Rio|\App\Models\Neo $entity
     * @return array
     */
    public static function generateSessionData($entity)
    {
        // Get current plan of entity
        $optionRoutes = [];
        $plan = self::getCurrentPlan($entity);

        // Get subscriber details
        $subscriber = $entity->subscriber()
            ->active()
            ->unexpired()
            ->first();

        if ($subscriber) {
            // Get plan service ids
            $planServiceIds = Subscription::subscriberList($subscriber->id)
                ->active()
                ->unexpired()
                ->pluck('plan_service_id')
                ->filter()
                ->toArray();

            // Get plan options service ids
            $planOptions = PlanService::list($planServiceIds)
                ->pluck('service_id')
                ->filter()
                ->toArray();

            // Prepare accessible routes via options
            $optionRoutes = Service::whereIn('id', $planOptions)
                ->pluck('route_name')
                ->filter()
                ->map(function ($item, $key) {
                    return $item . '.*';
                })
                ->toArray();
        }

        // Guard clause for non-existing plan
        if (empty($plan)) {
            return [];
        }

        // Get plan accessible routes
        return [
            'plan_id' => $plan->id,
            'plan_name' => $plan->name,
            'routes' => array_merge($plan->accessible_routes, $optionRoutes) ?? [],
            'plan_options' => $planOptions ?? [],
        ];
    }

    /**
     * Generate storage info session data
     *
     * @param \App\Models\Rio|\App\Models\Neo $entity
     * @return array
     */
    public static function getStorageInfo($entity)
    {
        // Identify entity type
        $entityType = CommonHelper::getEntityType($entity);

        // Prepare initial/default data
        $usedStorage = (int) Document::totalStorageUsed($entity);
        $default = [
            'max_storage' => 0,
            'used' => $usedStorage,
            'available' => 0
        ];

        // Identify document management service
        $service = Service::whereRouteName('document')->first();

        // Guard clause for non-existing service
        if (empty($service)) {
            return $default;
        }

        // Get service settings details
        $serviceSettings = $entity->service_settings()
            ->document()
            ->first();

        // Return storage info if record is available
        if (!empty($serviceSettings)) {
            return $serviceSettings->storage_info;
        }

        // Create service settings record if no record is available
        $settings = ServiceSetting::createServiceSetting($default, $entity, $service->id);

        // Get current plan of entity
        $plan = self::getCurrentPlan($entity);

        // Guard clause for non-existing plan
        if (empty($plan)) {
            return $default;
        }

        // Get plan service
        $planServices = PlanService::wherePlanId($plan->id)
            ->whereServiceId($service->id)
            ->whereType(PlanServiceType::PLAN)
            ->first();

        // Guard clause for non-existing plan service
        if (empty($planServices)) {
            return $default;
        }

        // Prepare storage info - Plan Based Only
        $storage = CommonHelper::convertToBytes($planServices->unit, (int) $planServices->value);

        // Get subscriber info
        $subscriber = $entity->subscriber()
            ->active()
            ->unexpired()
            ->first();

        if (!empty($subscriber)) {
            $subscriptions = Subscription::join('plan_services', 'subscriptions.plan_service_id', '=', 'plan_services.id')
                ->where('plan_services.service_id', $service->id)
                ->where('plan_services.type', PlanServiceType::OPTION)
                ->where('subscriptions.subscriber_id', $subscriber->id)
                ->active()
                ->unexpired()
                ->groupBy('subscriptions.plan_service_id')
                ->select('plan_services.*', DB::raw('SUM(subscriptions.quantity) As total_quantity'))
                ->get();

            if (!empty($subscriptions)) {
                foreach ($subscriptions as $subscription) {
                    // Prepare storage info
                    $storage = $storage + CommonHelper::convertToBytes($subscription->unit, ((int) $subscription->value * (int) $subscription->total_quantity));
                }
            }
        }

        $available = $storage - $usedStorage;
        $storageInfo = [
            'max_storage' => $storage,
            'used' => $usedStorage,
            'available' => $available < 0 ? 0 : $available,
        ];

        // Update storage info in record
        $settings->data = json_encode($storageInfo);
        $settings->save();

        return $storageInfo;
    }
}
