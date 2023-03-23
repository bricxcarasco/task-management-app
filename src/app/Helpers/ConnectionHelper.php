<?php

/**
 * Common Helper
 *
 * @author yns
 */

namespace App\Helpers;

use App\Enums\Connection\ConnectionStatuses;
use App\Enums\ServiceSelectionTypes;
use App\Models\NeoConnection;
use App\Models\RioConnectionUser;
use App\Objects\ServiceSelected;

/**
 * App\Helpers\ConnectionHelper
 *
 * @package AgrigoSystem
 * @subpackage PhpDocumentor
 */
class ConnectionHelper
{
    /**
     * Check if target entity is connected to authenticated session
     *
     * @param string $type - Target entity type
     * @param int $targetId - Target entity id
     * @param object $session - Session object
     * @return bool
     */
    public static function isConnectedToSession($type, $targetId, $session = null)
    {
        // Set default session
        if (empty($session)) {
            $session = ServiceSelected::getSelected();
        }

        // Initialize variables
        $entity = $session->data;
        $sessionId = $entity->id;

        // Check connection when both session and target is RIO
        if (!in_array(ServiceSelectionTypes::NEO, [$type, $session->type])) {
            return RioConnectionUser::query()
                ->selectRaw(
                    'COUNT(rio_connection_users.rio_connection_id) AS connection_count'
                )
                ->leftJoin('rio_connections', 'rio_connections.id', '=', 'rio_connection_users.rio_connection_id')
                ->whereIn('rio_connection_users.rio_id', [$targetId, $sessionId])
                ->where('rio_connections.status', '=', ConnectionStatuses::CONNECTED)
                ->whereNull('rio_connection_users.deleted_at')
                ->whereNull('rio_connections.deleted_at')
                ->groupBy('rio_connection_users.rio_connection_id')
                ->having('connection_count', '>', '1')
                ->exists();
        }

        // Prepare neo connection query
        $neoQuery = NeoConnection::whereStatus(ConnectionStatuses::CONNECTED);

        // Check connection when both session and target is NEO
        if (!in_array(ServiceSelectionTypes::RIO, [$type, $session->type])) {
            return $neoQuery
                ->whereNull('connection_rio_id')
                ->where(function ($subquery) use ($targetId, $sessionId) {
                    $subquery->where('neo_id', $targetId)
                        ->where('connection_neo_id', $sessionId);
                })
                ->orWhere(function ($subquery) use ($targetId, $sessionId) {
                    $subquery->where('connection_neo_id', $targetId)
                        ->where('neo_id', $sessionId);
                })
                ->exists();
        }

        switch ($session->type) {
            // Check connection when session is NEO and target is RIO
            case ServiceSelectionTypes::NEO:
                return $neoQuery
                    ->where(function ($subquery) use ($targetId, $sessionId) {
                        $subquery->where('connection_rio_id', $targetId)
                            ->where('neo_id', $sessionId);
                    })
                    ->exists();
            // Check connection when session is RIO and target is NEO
            case ServiceSelectionTypes::RIO:
                return $neoQuery
                    ->where(function ($subquery) use ($targetId, $sessionId) {
                        $subquery->where('neo_id', $targetId)
                            ->where('connection_rio_id', $sessionId);
                    })
                    ->exists();
        }

        return false;
    }
}
