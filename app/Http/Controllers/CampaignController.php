<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Utils\VideoUtils;
use App\Models\Video\Campaign;
use App\Repositories\Video\VideoRepositoryInterface;
use App\Repositories\Client\ClientRepositoryInterface;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ClientRepositoryInterface $clientInterface)
    {
        $campaign = $clientInterface->getFirstCampaignOfAuthenticatedClient();

        if (!$campaign) {
            return Inertia::render('DashboardClient', [
                'currentSideNavRoute' => 'client.campaigns',
                'currentSideNavName' => 'No campaign yet',
                'recentVideos' => [],
                'pendingVideos' => [],
                'unFinishedVideos' => [],
                'campaigns' => [],
                'currentCampaignId' => '',
                'company' => $clientInterface->getCompanyOfAuthenticatedUser()
            ]);
        }

        return redirect(route('client.campaigns.show', [
            'campaign' => $campaign->id
        ]));
    }

    /**
     * Display the specified resource.
     *
     * @param  App\Repositories\Video\VideoRepositoryInterface  $videoInterface
     * @param  App\Repositories\Client\ClientRepositoryInterface  $clientInterface
     * @param  string  $campaign
     * @return \Illuminate\Http\Response
     */
    public function show(
        VideoRepositoryInterface $videoInterface,
        ClientRepositoryInterface $clientInterface,
        Campaign $campaign
    ) {
        $company = $clientInterface->getCompanyOfAuthenticatedUser();

        $videos = $videoInterface->getVideos(
            [
                'id',
                'thumbnail',
                'name',
                'created_at',
                'updated_at',
                'status',
                'is_visited'
            ],
            true,
            $company->id,
            $campaign->id
        );

        return Inertia::render('DashboardClient', [
            'currentSideNavRoute' => 'client.campaigns',
            'currentSideNavName' => $campaign->name,
            'recentVideos' => $videos->where('status', VideoUtils::STATUS_FINISHED)->values(),
            'pendingVideos' => $videos->whereIn('status', [
                VideoUtils::STATUS_PENDING,
                VideoUtils::STATUS_PROPOSAL_RECEIVED,
                VideoUtils::STATUS_VIDEO_RECEIVED,
                VideoUtils::STATUS_STORYBOARD_RECEIVED
            ])->values(),
            'unFinishedVideos' => $videos->where('status', VideoUtils::STATUS_ORDERED)->values(),
            'campaigns' => $videoInterface
                ->getCampaignsOfAuthenticatedClient(
                    $clientInterface->getAuthenticatedClient(['id']),
                    ['id', 'name', 'created_at']
                ),
            'currentCampaignId' => $campaign->id,
            'company' => $company
        ]);
    }
}
