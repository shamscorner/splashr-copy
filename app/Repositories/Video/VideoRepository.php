<?php

namespace App\Repositories\Video;

use App\Models\Client;
use App\Models\Folder;
use App\Models\Order\Order;
use App\Models\Video\Video;
use App\Models\Video\Format;
use App\Models\Video\Language;
use App\Models\Video\Purpose;
use App\Models\Video\Platform;
use App\Utils\GoogleDriveFileUtils;
use Illuminate\Database\Eloquent\Collection;

class VideoRepository implements VideoRepositoryInterface
{
    /**
     * Get all videos including pending videos if isPendingIncluded = true
     * 
     * @param array $selectedFields
     * @param bool $isDescending
     * @param string $clientId: ''
     * @param string $campaignId: ''
     * @param bool $isOrderDetailsIncluded: false
     * 
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getVideos(
        array $selectedFields,
        bool $isDescending,
        string $clientId = '',
        string $campaignId = '',
        bool $isOrderDetailsIncluded = false
    ): Collection {
        return Video::when($clientId, function ($query, $clientId) {
            return $query->where('company_id', $clientId);
        })
            ->when($campaignId, function ($query, $campaignId) {
                return $query->where('campaign_id', $campaignId);
            })
            ->when($isOrderDetailsIncluded, function ($query) {
                return $query->with('order');
            })
            ->with('order.creativeDocuments')
            ->select($selectedFields)
            ->orderBy('updated_at', $isDescending ? 'desc' : 'asc')
            ->get();
    }

    /**
     * Get all publish platforms including non-verified if isVerifiedIncluded = true
     * 
     * @param bool $isVerifiedIncluded
     * 
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPublishPlatforms(bool $isVerifiedIncluded): Collection
    {
        if ($isVerifiedIncluded) {
            return Platform::select('id', 'name', 'is_verified')->get();
        }
        return Platform::where('is_verified', true)
            ->select('id', 'name', 'is_verified')
            ->get();
    }

    /**
     * Get all languages including non-verified if isVerifiedIncluded = true
     * 
     * @param bool $isVerifiedIncluded
     * 
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAllLanguages(bool $isVerifiedIncluded): Collection
    {
        if ($isVerifiedIncluded) {
            return Language::select('id', 'name', 'is_verified')->get();
        }
        return Language::where('is_verified', true)
            ->select('id', 'name', 'is_verified')
            ->get();
    }

    /**
     * Get all purposes 
     * 
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAllPurposes(): Collection
    {
        return Purpose::select('id', 'name')->get();
    }

    /**
     * Get all formats including icon if $isIcon = true
     * 
     * @param bool $isIcon 
     * 
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAllFormats(bool $isIcon = false): Collection
    {
        $selects = ['id', 'name', 'ratio', 'viewport'];

        if ($isIcon) {
            array_push($selects, 'icon');
        }

        return Format::select($selects)->get();
    }

    /**
     * Create a video
     * 
     * @param array $data 
     * 
     * @return App\Models\Video\Video
     */
    public function createAVideo(array $data): Video
    {
        return Video::create($data);
    }

    /**
     * Update is-visited attribute of a specified video
     * 
     * @param string $videoId 
     * @param bool $isVisited 
     */
    public function updateIsVisited(string $videoId, bool $isVisited = false)
    {
        Video::where('id', $videoId)->update([
            'is_visited' => $isVisited
        ]);
    }

    /**
     * Get A video data with some relations if is included
     * 
     * @param string $videoId
     * @param array $selects: [*]
     * @param bool $isPlatformsIncluded: false
     * @param bool $isPurposesIncluded: false
     * @param bool $isFormatsIncluded: false
     * @param bool $isVerifiedPlatformsIncluded: false
     * @param bool $isCampaignIncluded: false
     * 
     * @return App\Models\Video\Video
     */
    public function getAVideo(
        string $videoId,
        array $selects = ['*'],
        bool $isPlatformsIncluded = false,
        bool $isPurposesIncluded = false,
        bool $isFormatsIncluded = false,
        bool $isVerifiedPlatformsIncluded = false,
        bool $isCampaignIncluded = false,
        bool $isClientIncluded = false
    ): Video {
        return Video::where('id', $videoId)
            ->when($isPlatformsIncluded, function ($query) use ($isVerifiedPlatformsIncluded) {
                if ($isVerifiedPlatformsIncluded) {
                    return $query->with('platforms', function ($queryWithPlatforms) {
                        $queryWithPlatforms->select('platforms.id as id', 'platforms.name as name', 'platforms.is_verified as is_verified');
                    });
                } else {
                    return $query->with('platforms', function ($queryPlatforms) {
                        return $queryPlatforms->where('is_verified', true)->select('platforms.id as id', 'platforms.name as name', 'platforms.is_verified as is_verified');
                    });
                }
            })
            ->when($isPurposesIncluded, function ($query) {
                return $query->with('purposes', function ($queryPurpose) {
                    $queryPurpose->select('purposes.id as id', 'purposes.name as name');
                });
            })
            ->when($isFormatsIncluded, function ($query) {
                return $query->with('formats', function ($queryFormat) {
                    $queryFormat->select('formats.id as id', 'icon', 'formats.name as name', 'ratio', 'viewport');
                });
            })
            ->when($isCampaignIncluded, function ($query) {
                return $query->with('campaign', function ($queryCampaign) {
                    $queryCampaign->select('campaigns.id as id', 'campaigns.name as name');
                });
            })
            ->when($isClientIncluded, function ($query) {
                return $query->with('client');
            })
            ->select($selects)
            ->first();
    }

    /**
     * Get all the campaigns of a authenticated client
     * 
     * @param App\Models\Client $client
     * @param array $selects: ['*']
     * 
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getCampaignsOfAuthenticatedClient(Client $client, array $selects = ['*']): Collection
    {
        $selectsForQuery = [];
        if ($selects[0] == '*') {
            array_push($selectsForQuery, '*');
        } else {
            foreach ($selects as $select) {
                array_push($selectsForQuery, 'campaigns.' . $select . ' as ' . $select);
            }
        }

        return $client->campaigns()
            ->select($selectsForQuery)
            ->get()
            ->map(function ($campaign) {
                return $campaign->makeHidden('pivot');
            });
    }

    /**
     * Get the project folder of google drive for a specified video
     * 
     * @param string $videoId
     * @param array $selects: ['*']
     * 
     * @return mixed;
     */
    public function getVideoProjectFolder(string $videoId, array $selects = ['*'])
    {
        return Folder::where('folderable_id', $videoId)
            ->whereNotIn('name', GoogleDriveFileUtils::PROJECT_FOLDERS)
            ->select($selects)
            ->first();
    }

    /**
     * Get video details of a specific order
     * 
     * @param string $orderId
     * 
     * @return App\Models\Video\Video
     */
    public function getVideoByOrderId(string $orderId): Video
    {
        return Video::where('id', function ($query) use ($orderId) {
            $query->select('video_id')
                ->from('orders')
                ->where('id', $orderId)
                ->whereColumn('orders.video_id', 'videos.id')
                ->limit(1);
        })->first();
    }

    /**
     * Get order by a specific video id
     * 
     * @param string $videoId
     * 
     * @return App\Models\Order\Order
     */
    public function getOrderByVideoId(string $videoId): Order
    {
        return Order::query()
            ->where('video_id', $videoId)
            ->with('creativeDocuments')->first();
    }
}
