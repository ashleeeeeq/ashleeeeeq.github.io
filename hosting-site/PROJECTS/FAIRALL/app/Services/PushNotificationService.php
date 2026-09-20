<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class PushNotificationService
{
    private ?Factory $factory = null;

    public function __construct()
    {
        $credentials = config('services.fcm.credentials');

        if ($credentials && file_exists($credentials)) {
            $this->factory = (new Factory)->withServiceAccount($credentials);
        } else {
            Log::warning('Firebase credentials not found at: ' . $credentials);
        }
    }

    public function send(User $user, string $title, string $body, array $data = []): bool
    {
        if (!$this->factory) {
            return false;
        }

        $tokens = DeviceToken::where('user_id', $user->id)
            ->where('is_active', true)
            ->pluck('token')
            ->values()
            ->all();

        if (empty($tokens)) {
            return false;
        }

        $messaging = $this->factory->createMessaging();

        $success = true;

        foreach ($tokens as $token) {
            try {
                $message = CloudMessage::new()
                    ->withToken($token)
                    ->withData(array_filter(array_merge($data, [
                        'title' => $title,
                        'body' => $body,
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    ]), fn($v) => $v !== null))
                    ->withHighestPossiblePriority();

                $messaging->send($message);
            } catch (FirebaseException $e) {
                Log::error('FCM send failed', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
                $success = false;
            }
        }

        return $success;
    }

    public function sendToToken(string $token, string $title, string $body, array $data = []): bool
    {
        if (!$this->factory) {
            return false;
        }

        $messaging = $this->factory->createMessaging();

        try {
            $message = CloudMessage::new()
                ->withToken($token)
                ->withData(array_filter(array_merge($data, [
                    'title' => $title,
                    'body' => $body,
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ]), fn($v) => $v !== null))
                ->withHighestPossiblePriority();

            $messaging->send($message);
            return true;
        } catch (FirebaseException $e) {
            Log::error('FCM send failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function sendToMultiple(array $users, string $title, string $body, array $data = []): bool
    {
        if (!$this->factory) {
            return false;
        }

        $userIds = collect($users)->pluck('id');
        $tokens = DeviceToken::whereIn('user_id', $userIds)
            ->where('is_active', true)
            ->pluck('token')
            ->values()
            ->all();

        if (empty($tokens)) {
            return false;
        }

        $messaging = $this->factory->createMessaging();

        $success = true;

        foreach ($tokens as $token) {
            try {
                $message = CloudMessage::new()
                    ->withToken($token)
                    ->withData(array_filter(array_merge($data, [
                        'title' => $title,
                        'body' => $body,
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    ]), fn($v) => $v !== null))
                    ->withHighestPossiblePriority();

                $messaging->send($message);
            } catch (FirebaseException $e) {
                Log::error('FCM bulk send failed', ['error' => $e->getMessage()]);
                $success = false;
            }
        }

        return $success;
    }
}