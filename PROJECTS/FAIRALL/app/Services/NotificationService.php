<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationService
{
    public function __construct(
        private PushNotificationService $pushService
    ) {}

    public function send(
        User $user,
        string $type,
        string $title,
        string $message,
        ?string $actionUrl = null,
        array $additionalData = [],
        bool $sendPush = true
    ): void {
        $this->createInApp($user, $type, $title, $message, $actionUrl, $additionalData);

        if ($sendPush) {
            $this->pushService->send($user, $title, $message, [
                'type' => $type,
                'action_url' => $actionUrl,
                ...$additionalData,
            ]);
        }
    }

    public function sendToMultiple(
        array $users,
        string $type,
        string $title,
        string $message,
        ?string $actionUrl = null,
        array $additionalData = [],
        bool $sendPush = true
    ): void {
        $now = now();
        $records = [];
        $notifiableUsers = [];

        foreach ($users as $user) {
            $records[] = [
                'id' => (string) Str::uuid(),
                'type' => $type,
                'notifiable_type' => get_class($user),
                'notifiable_id' => $user->id,
                'data' => json_encode([
                    'title' => $title,
                    'message' => $message,
                    'action_url' => $actionUrl,
                    ...$additionalData,
                ]),
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if ($sendPush) {
                $notifiableUsers[] = $user;
            }
        }

        DB::table('notifications')->insert($records);

        if ($sendPush && !empty($notifiableUsers)) {
            $this->pushService->sendToMultiple($notifiableUsers, $title, $message, [
                'type' => $type,
                'action_url' => $actionUrl,
                ...$additionalData,
            ]);
        }
    }

    private function createInApp(
        User $user,
        string $type,
        string $title,
        string $message,
        ?string $actionUrl = null,
        array $additionalData = []
    ): void {
        $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => $type,
            'data' => [
                'title' => $title,
                'message' => $message,
                'action_url' => $actionUrl,
                ...$additionalData,
            ],
        ]);
    }
}
