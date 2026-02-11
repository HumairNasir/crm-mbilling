<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Send notification to ALL users (all roles).
     */
    public static function sendToAll($type, $title, $message, $actionUrl = null, $triggeredBy = null)
    {
        $userIds = User::pluck('id')->toArray();
        self::sendToMany($userIds, $type, $title, $message, $actionUrl, $triggeredBy);
    }

    /**
     * Send notification to multiple users at once.
     */
    public static function sendToMany(array $userIds, $type, $title, $message, $actionUrl = null, $triggeredBy = null)
    {
        $now = now();
        $records = [];

        foreach (array_unique($userIds) as $userId) {
            $records[] = [
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'icon' => $type === Notification::TYPE_BATCH_STARTED ? 'batch' : 'convert',
                'action_url' => $actionUrl,
                'triggered_by' => $triggeredBy,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($records)) {
            Notification::insert($records);
        }
    }

    /**
     * Notify all roles about a new Auto-Pilot batch.
     */
    public static function batchStarted($totalAssigned, $triggeredById)
    {
        $triggeredBy = User::find($triggeredById);
        $name = $triggeredBy ? $triggeredBy->name : 'System';

        self::sendToAll(
            Notification::TYPE_BATCH_STARTED,
            'Auto-Pilot Batch Started',
            "{$name} started a new batch — {$totalAssigned} tasks assigned.",
            '/team-tasks',
            $triggeredById,
        );
    }

    /**
     * Notify all roles about a lead conversion.
     */
    public static function leadConverted($clientName, $officeName, $triggeredById)
    {
        $triggeredBy = User::find($triggeredById);
        $repName = $triggeredBy ? $triggeredBy->name : 'Someone';

        self::sendToAll(
            Notification::TYPE_LEAD_CONVERTED,
            'Lead Converted to Client',
            "{$repName} converted \"{$officeName}\" → Client \"{$clientName}\".",
            '/clients',
            $triggeredById,
        );
    }
}
