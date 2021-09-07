<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Utils\VideoUtils;
use App\Models\Video\Video;
use Illuminate\Http\Request;
use App\Services\Video\VideoService;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Video\VideoRepositoryInterface;
use App\Repositories\Client\ClientRepositoryInterface;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(
        VideoRepositoryInterface $videoInterface,
        ClientRepositoryInterface $clientInterface
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
            '',
            true
        );

        return Inertia::render('DashboardClient', [
            'currentSideNavRoute' => 'client.videos',
            'currentSideNavName' => 'All Videos',
            'recentVideos' => $videos->where('status', VideoUtils::STATUS_FINISHED)->values(),
            'pendingVideos' => $videos->whereIn('status', [
                VideoUtils::STATUS_PENDING,
                VideoUtils::STATUS_PROPOSAL_RECEIVED,
                VideoUtils::STATUS_VIDEO_RECEIVED,
                VideoUtils::STATUS_STORYBOARD_RECEIVED
            ])->values(),
            'unFinishedVideos' => $videos->where('status', VideoUtils::STATUS_ORDERED)->values(),
            'company' => $company
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(
        Request $request,
        VideoRepositoryInterface $videoInterface,
        ClientRepositoryInterface $clientInterface
    ) {
        $companyId = $request->query('company');
        $isFromManager = $request->query('isFromManager');

        $clientId = null;
        $isSidebar = "true";

        if ($isFromManager) {
            $clientId = $clientInterface->getTheFirstClientOfACompany($companyId)->id;
            $isSidebar = "false";
        } else {
            $clientId = $clientInterface->getClientFromUserId(Auth::id())->id;
        }

        $video = $videoInterface->createAVideo([
            'name' => 'Untitled Video',
            'client_id' => $clientId,
            'company_id' => $companyId
        ]);

        return redirect()->route('videos.steps', [
            'video' => $video->id,
            'step' => VideoUtils::STEP_GENERAL,
            'sidebar' => $isSidebar
        ]);
    }

    public function videoSteps(Request $request, $video)
    {
        $isEditVideo = $request->query('edit');
        $step = $request->query('step');
        $validStep = VideoUtils::toValidStep($step);

        if (in_array($validStep, VideoUtils::STEPS)) {
            $videoService = new VideoService();

            $videoServiceData = $videoService->getVideoStepData($step, $video);
            $videoData = $videoServiceData['videoData'];
            $data = $videoServiceData['data'];

            $data['remembered'] = $videoService->getLastRememberedAnswers($videoData['client_id']);

            return Inertia::render('Video/Steps/' . $step, [
                'currentSideNavRoute' => 'client.videos',
                'currentSideNavName' => 'Brief',
                'isEdit' => $isEditVideo ? ($isEditVideo === 'true' ? true : false) : false,
                'data' => $data,
                'video' => $videoData,
                'videoSteps' => VideoUtils::STEPS,
                'company' => $videoServiceData['company'],
                'isSideNavVisible' => $request->query('sidebar')
            ]);
        }
        return abort(404);
    }

    public function show(
        Request $request,
        VideoRepositoryInterface $videoInterface,
        ClientRepositoryInterface $clientInterface,
        $video
    ) {
        $campaignId = $request->query('campaign');

        $currentTabName = $request->query('currentTab');

        $videoInterface->updateIsVisited($video, true);

        $videoData = $videoInterface->getAVideo(
            $video,
            ['id', 'client_id', 'company_id', 'name', 'service_offer', 'audience', 'key_message', 'is_voice_over', 'other_requirements', 'status', 'session_type'],
            true,
            true,
            true,
            true,
            false,
            $campaignId ? true : false
        );

        $props = [
            'currentSideNavRoute' => $campaignId ? 'client.campaigns' : 'client.videos',
            'currentSideNavName' => $videoData->name,
            'currentCampaignId' => $campaignId,
            'currentTabName' => $currentTabName,
            'video' => $videoData,
            'videoSteps' => VideoUtils::STEPS,
            'assets' => (new VideoService())->getFoldersData($videoData),
            'company' => $clientInterface->getCompanyOfAuthenticatedUser(),
            'order' => $videoInterface->getOrderByVideoId($video)
        ];

        if ($campaignId) {
            $props['campaigns'] = $videoInterface
                ->getCampaignsOfAuthenticatedClient(
                    $videoData->client,
                    ['id', 'name', 'created_at']
                );
        }

        return Inertia::render('Video/Preview', $props);
    }

    public function isProjectFoldersCreated(Video $video)
    {
        $folders = (new VideoService())->getFoldersData($video);

        $isCreated = false;

        foreach ($folders as $folder) {
            $isCreated = $folder ? true : false;
        }

        return response()->json([
            'isCreated' => $isCreated
        ]);
    }
}
