<?php

namespace App\Objects;

use App\Enums\NeoBelongStatuses;

class Tasks
{
    /**
     * Get selectable time options
     *
     * @return array
     */
    public static function getTimeSelections()
    {
        $timeSelections = [];

        for ($hours = 0; $hours < 24; $hours++) {
            for ($mins = 0; $mins < 60; $mins += 30) {
                /** @phpstan-ignore-next-line */
                $time = str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($mins, 2, '0', STR_PAD_LEFT);

                $timeSelections += [$time => $time];
            }
        }

        return $timeSelections;
    }

    /**
     * Get selectable service options
     *
     * @param \App\Models\Rio $rio Logged-in RIO user
     * @param mixed $default
     * @return array
     */
    public static function getServiceSelections($rio, $default = null)
    {
        $selections = [];

        // Set default value if necessary
        if (!empty($default)) {
            $selections = ['' => $default];
        }

        // Get RIO selection
        $rioSelections = [$rio->toArray()];

        // Get NEO selections
        $neoSelections = $rio->neos()
            ->whereStatus(NeoBelongStatuses::AFFILIATED)
            ->get()
            ->toArray();

        foreach (array_merge($rioSelections, $neoSelections) as $selection) {
            if (isset($selection['full_name'])) {
                $selections += [
                    'rio_' . $selection['id'] => $selection['full_name']
                ];
            }

            if (isset($selection['organization_name'])) {
                $selections += [
                    'neo_' . $selection['id'] => $selection['organization_name']
                ];
            }
        }

        return $selections;
    }
}
