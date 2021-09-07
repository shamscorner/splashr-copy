<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{
    public function test()
    {
        dump(config('frameio.token'));

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . config('frameio.token'),
        ])->get('https://api.frame.io/v2/assets/fdbe4cc6-a377-4b46-bd64-323a4b5ee3df');

        $response = $response->json();

        dump($response);

        dd('stopped');

        // dd($response['label']);
    }

    public function frameIoWebhook(Request $request)
    {
        // $signature = $request->header('X-Frameio-Signature');
        // Log::info('Signature: ' . $signature);
        Log::info('Content: ' . $request->getContent());

        // $computedSignature = hash_hmac(
        //     'sha256',
        //     'v0:' . time() . ':' . $request->getContent(),
        //     env('FRAME_IO_WEBHOOK_SECRET')
        // );

        // if (hash_equals($signature, 'v0=' . $computedSignature)) {
        //     Log::info('Content: ' . $request->getContent());
        // }

        $content = json_decode($request->getContent());

        Log::info('Resource ID: ' . $content->resource->id);

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . config('frameio.token'),
        ])->get('https://api.frame.io/v2/assets/' . $content->resource->id);

        Log::info('Response: ' . $response->body());

        $response = $response->json();

        // Log::info('Updated Label: ' . $response['label']);
    }
}
