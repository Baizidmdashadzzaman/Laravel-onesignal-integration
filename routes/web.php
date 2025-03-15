<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;


Route::get('/', function () {
    return view('welcome');
});


Route::get('/send-notification', function (Illuminate\Http\Request $request) {
    try {
        $contents = "This is github of Baizid MD Ashadzzaman";
        $subcriptionIds = ["7bdbd5a3-5dd9-4d8d-a4f7-d36162564548"];
        $url = 'https://github.com/Baizidmdashadzzaman';

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . env('ONESIGNAL_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://onesignal.com/api/v1/notifications', [
            'app_id' => env('ONESIGNAL_APP_ID'),
            'included_segments' => ['All'],
            'included_player_ids' => $subcriptionIds,
            'contents' => ['en' => $contents],
            'url' => $url,
        ]);

        return response()->json($response->json());
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});
