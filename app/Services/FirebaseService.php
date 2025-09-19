<?php

namespace App\Services;

use Google\Client as GoogleClient;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use App\Models\UserDevice;
use App\Models\Blog;

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

        return $response;
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




public function sendNotificationAllByBlogId($id)
{
    $blog = Blog::with(['image','blog_category'])->where('id',$id)->first();
            if(!$blog)
            {
                return $this->sendError('blog not found');
               
            }

     $notificationData = [
        "id" => "".$blog->id,
"title" => $blog->title, 
  "description" => $blog->description,
  "source_link" => $blog->source_link,
  "image" => @$blog->image->image ,
  "created_at" => $blog->created_at,
  "category" => $blog->blog_category->category->name,

    ];

     $tokens = UserDevice::pluck('token')->toArray();

     $responses = [];

    foreach($tokens as $token) {
        try {

        $responses[] = $this->sendNotification(
            $token, 
            "For You", 
            $blog->title, 
            image:$notificationData["image"],
            data : $notificationData
        );

    }

    catch (RequestException $e) {
    // 🔹 Network / HTTP errors
    if ($e->hasResponse()) {
        $statusCode = $e->getResponse()->getStatusCode();
        $errorBody  = json_decode($e->getResponse()->getBody(), true);
 \Log::warning("error body: {$e->getResponse()->getBody()}");
        if ($statusCode === 404 || $statusCode === 401 || $statusCode === 400) {
            // Token expired or not found
            // ❌ Remove from DB
            UserDevice::where('token', $token)->delete();
            \Log::warning("FCM token expired: { $token}");
        } else {
            // Other FCM error
            \Log::error("FCM Error {$statusCode}: ");
        }
    } else {
        // 🔹 No response (timeout, DNS, connection issue)
        \Log::error("FCM request failed: " . $e->getMessage());
    }

        }




        
    }
     return $responses;
}





}
