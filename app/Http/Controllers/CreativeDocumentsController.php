<?php

namespace App\Http\Controllers;

use App\Models\Order\CreativeDocument;
use App\Models\Order\Order;
use App\Utils\AppUtils;
use Inertia\Inertia;

class CreativeDocumentsController extends Controller
{
    public function googleDocFormOfCreativeProposal($order, $doc)
    {
        $this->updateIsChangedToFalse($doc);

        $orderData = Order::where('id', $order)->with('video:id,name')->first();

        return Inertia::render('Storyboard/GoogleDoc', [
            'currentSideNavName' => $orderData->video->name,
            'videoId' => $orderData->video->id,
            'fromTab' => AppUtils::TAB_DOCUMENTS
        ]);
    }

    public function googleSlideFormOfStoryboard($order, $slide)
    {
        $this->updateIsChangedToFalse($slide);

        $orderData = Order::where('id', $order)->with('video:id,name')->first();

        return Inertia::render('Storyboard/GoogleSlide', [
            'currentSideNavName' => $orderData->video->name,
            'videoId' => $orderData->video->id,
            'fromTab' => AppUtils::TAB_DOCUMENTS
        ]);
    }

    public function frameIoFormOfStoryboard($order, $id)
    {
        $this->updateIsChangedToFalse($id);

        $orderData = Order::where('id', $order)->with('video:id,name')->first();

        return Inertia::render('Storyboard/FrameIo', [
            'currentSideNavName' => $orderData->video->name,
            'videoId' => $orderData->video->id,
            'fromTab' => AppUtils::TAB_VIDEOS
        ]);
    }

    private function updateIsChangedToFalse($GDocumentId)
    {
        CreativeDocument::where('document_id', $GDocumentId)->update([
            'is_changed' => false
        ]);
    }
}
