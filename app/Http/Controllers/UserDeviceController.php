<?php

namespace App\Http\Controllers;

use App\Models\UserDevice;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserDeviceRequest;
use App\Http\Requests\UpdateUserDeviceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\FirebaseService;
class UserDeviceController extends Controller
{

    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return "user devices index";
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $request->validate(['token' => 'required|string']);
        UserDevice::updateOrCreate(
            ['token' => $request->token,],
            [
                                    'token' => $request->token,
                                    'name' => $request->name,
                                    'language' => $request->language ? $request->language : 'en',
                                    'meta' => $request->meta
                                ],

                            );
    return response()->json(['message' => 'Token stored successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(UserDevice $userDevice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserDevice $userDevice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserDeviceRequest $request, UserDevice $userDevice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserDevice $userDevice)
    {
        //
    }



public function sendNotification(Request $request)
{

  $request->validate([
    'title' => 'required|string',
    'body' => 'required|string'
]);


  // $deviceToken = 'ew8AU1IfSii2R07QOMY2z9:APA91bGHSUNN3QL557cMFxmYU4Lq1owpBwTaCTkzN1gCLaUkuKdystG9cEYyECcKk6BB1p-C5oiA4HRBeTW55KpHgdHxmzFUzAUYxyZyGzWInT3Vf-09bVQ';
  //       $title = 'Hello from Laravel';
  //       $body  = 'This is a test notification using FCM API v1';
  //       $data  = ['customKey' => 'customValue'];

  //       $response = $this->firebase->sendNotification($deviceToken, $title, $body, $data);

  //       return response()->json($response);

  $request->data =  $request->data ?  $request->data : ["title"=>$request->title];

    $tokens = UserDevice::pluck('token')->toArray();

    $responses = [];
    foreach ($tokens as $token) {
        $responses[$token] = $this->firebase->sendNotification(
            $token, 
            $request->title, 
            $request->body, 
            image:$request->image,
            data : json_decode($request->data, true)
        );
    }
    return response()->json($responses);


}




}
