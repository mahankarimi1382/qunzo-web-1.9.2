<?php

namespace App\Traits;

use Exception;
use Google\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

trait FcmTrait
{
    protected function sendFcmNotification($token, $title, $body, $data = [])
    {
        try {
            // Get firebase credentials from plugin
            $firebase = plugin_active('Firebase');
            $firebaseData = json_decode($firebase->data, true);

            $jsonPath = public_path($firebaseData['upload_account_json']);
            $serviceAccount = json_decode(file_get_contents($jsonPath), true);

            // Get project id and current timestamp
            $projectId = $serviceAccount['project_id'];
            $now = time();

            // Jwt header 
            $jwtHeader = base64_encode(json_encode([
                'alg' => 'RS256',
                'typ' => 'JWT',
            ]));

            $jwtClaim = base64_encode(json_encode([
                'iss'   => $serviceAccount['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud'   => 'https://oauth2.googleapis.com/token',
                'iat'   => $now,
                'exp'   => $now + 3600,
            ]));

            // Generate open ssl signature
            openssl_sign(
                "$jwtHeader.$jwtClaim",
                $signature,
                $serviceAccount['private_key'],
                'sha256'
            );

            $jwt = $jwtHeader . '.' . $jwtClaim . '.' . base64_encode($signature);

            $tokenResponse = Http::asForm()->post(
                'https://oauth2.googleapis.com/token',
                [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion'  => $jwt,
                ]
            );

            if ($tokenResponse->successful()) {
                $accessToken = $tokenResponse->json('access_token');
                $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

                $payload = [
                    'message' => [
                        'token' => $token,
                        'notification' => [
                            'title' => $title,
                            'body'  => $body,
                        ],
                        'data' => [
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                            'id' => '1',
                            'status' => 'done',
                        ],
                    ],
                ];

                $response = Http::withToken($accessToken)
                    ->post($url, $payload);
            }

            Log::info('FCM Token Response: ' . json_encode($tokenResponse->json()));
        } catch (\Throwable $e) {
            Log::error('FCM Notification Error: ' . $e->getMessage());
        }
    }
}
