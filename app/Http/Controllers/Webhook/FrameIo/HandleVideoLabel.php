<?php

namespace App\Http\Controllers\Webhook\FrameIo;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Video\VideoItem;
use App\Utils\VideoItemUtils;
use Illuminate\Support\Facades\Http;

class HandleVideoLabel extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $content = json_decode($request->getContent());

        Log::info('Resource ID: ' . $content->resource->id);

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . config('frameio.token'),
        ])->get('https://api.frame.io/v2/assets/' . $content->resource->id);

        $response = $response->json();

        Log::info('Updated Label: ' . $response['label']);

        if ($response) {
            $this->updateVideoItemStatus($content->resource->id, $response['label']);
        }
    }

    private function updateVideoItemStatus($resourceId, $status)
    {
        if ($resourceId) {
            VideoItem::query()
                ->where('id', $resourceId)
                ->update([
                    'status' => VideoItemUtils::VIDEO_ITEM_STATUS[$status]
                ]);
        }
    }
}
