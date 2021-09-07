<?php

namespace App\Http\Controllers\Google;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Google\GoogleSlideAPIService;

class GoogleSlideAPIController extends Controller
{
    private $service;

    public function __construct()
    {
        $this->service = new GoogleSlideAPIService();
    }

    public function getSlidesOfAPresentation($presentationId)
    {
        $slides = $this->service->getSlides($presentationId);

        if ($slides) {
            return response()->json($slides, 200);
        }

        return response()->json([], 200);
    }

    public function getThumbnailOfASlide($presentationId, $slideId)
    {
        $thumbnailWrapper = $this->service->getPresentationPageThumbnail($presentationId, $slideId);

        if ($thumbnailWrapper) {
            return response()->json([
                'url' => $thumbnailWrapper->contentUrl
            ], 200);
        }

        return response()->json(false, 200);
    }
}
