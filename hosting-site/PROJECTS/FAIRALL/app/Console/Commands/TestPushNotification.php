<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\NotificationService;
use App\Services\PushNotificationService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:test-push-notification')]
#[Description('Send a test notification (in-app + push)')]
class TestPushNotification extends Command
{
    public function handle(PushNotificationService $pushService, NotificationService $notificationService)
    {
        $mode = $this->choice('Send by user ID or direct token?', ['user', 'token'], 'user');

        $title = $this->ask('Notification title', 'Test Notification');
        $body = $this->ask('Notification body', 'This is a test notification');
        $type = $this->ask('Notification type', 'system');

        if ($mode === 'user') {
            $userId = $this->ask('Enter user ID');
            $user = User::find($userId);

            if (!$user) {
                $this->error('User not found');
                return 1;
            }

            $this->info("Sending to user {$user->id} ({$user->email})...");

            $notificationService->send($user, $type, $title, $body, null, [], true);

            $this->info('In-app notification + push sent');
            return 0;
        }

        $token = $this->ask('Enter FCM device token');
        $this->info("Sending to token...");
        $result = $pushService->sendToToken($token, $title, $body, ['type' => $type]);

        if ($result) {
            $this->info('Push notification sent successfully');
            return 0;
        }

        $this->error('Failed to send push notification');
        return 1;
    }
}
