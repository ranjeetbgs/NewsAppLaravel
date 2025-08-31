<?php

namespace App\Services;

use Google\Client as GoogleClient;
use GuzzleHttp\Client as GuzzleClient;

class FirebaseService
{
    protected $googleClient;
    protected $httpClient;
    protected $projectId;

    public function __construct()
    {
        $this->googleClient = new GoogleClient();
        $this->googleClient->setAuthConfig(config('services.firebase.credentials'));
        $this->googleClient->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $this->projectId = config('services.firebase.project_id');

        $this->httpClient = new GuzzleClient([
            'base_uri' => 'https://fcm.googleapis.com'
        ]);
    }

    /**
     * Send notification to a specific device token.
     */
    public function sendNotification($deviceToken, $title, $body, $image="", array $data = [])
    {
        // 1. Get access token from service account
        $accessToken = $this->googleClient->fetchAccessTokenWithAssertion()['access_token'];

        // 2. Prepare FCM message payload
        $payload = [
            'message' => [
                'token' => $deviceToken,
                'notification' => [
                    'title' => $title,
                    'body'  => $body
                ],
               

                'data' => $data
            ]
        ];

        if($image) $payload["message"]["android"] = [ "notification"=>[ "image"=>$image ] ];



        // 3. Send to FCM endpoint
        $response = $this->httpClient->post("/v1/projects/{$this->projectId}/messages:send", [
            'headers' => [
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type'  => 'application/json'
            ],
            'json' => $payload
        ]);

        return json_decode($response->getBody(), true);
    }


    public function sendToTopic($topic, $title, $body, array $data = [])
{
    $accessToken = $this->googleClient->fetchAccessTokenWithAssertion()['access_token'];

    $message = [
        'message' => [
            'topic' => $topic,
            'notification' => [
                'title' => $title,
                'body'  => $body
            ],
           // 'data' => $data
        ]
    ];

    $response = $this->httpClient->post("/v1/projects/{$this->projectId}/messages:send", [
        'headers' => [
            'Authorization' => "Bearer {$accessToken}",
            'Content-Type'  => 'application/json'
        ],
        'json' => $message
    ]);

    return json_decode($response->getBody(), true);
}

}
