<?php

namespace App\Repositories\Video;

use App\Models\Client;
use App\Models\Order\Order;
use App\Models\Video\Video;
use Illuminate\Database\Eloquent\Collection;

interface VideoRepositoryInterface
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
    ): Collection;

    /**
     * Get all publish platforms including non-verified if isVerifiedIncluded = true
     * 
     * @param bool $isVerifiedIncluded
     * 
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getPublishPlatforms(bool $isVerifiedIncluded): Collection;

    /**
     * Get all languages including non-verified if isVerifiedIncluded = true
     * 
     * @param bool $isVerifiedIncluded
     * 
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAllLanguages(bool $isVerifiedIncluded): Collection;

    /**
     * Get all purposes 
     * 
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAllPurposes(): Collection;

    /**
     * Get all formats including icon if $isIcon = true
     * 
     * @param bool $isIcon 
     * 
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAllFormats(bool $isIcon = false): Collection;

    /**
     * Create a video
     * 
     * @param array $data 
     * 
     * @return App\Models\Video\Video
     */
    public function createAVideo(array $data): Video;

    /**
     * Update is-visited attribute of a specified video
     * 
     * @param string $videoId 
     * @param bool $isVisited 
     */
    public function updateIsVisited(string $videoId, bool $isVisited = false);

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
        bool $isCampaignIncluded = false
    ): Video;

    /**
     * Get all the campaigns of a authenticated client
     * 
     * @param App\Models\Client $client
     * @param array $selects: ['*']
     * 
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getCampaignsOfAuthenticatedClient(Client $client, array $selects = ['*']): Collection;


    /**
     * Get the project folder of google drive for a specified video
     * 
     * @param string $videoId
     * @param array $selects: ['*']
     * 
     * @return mixed;
     */
    public function getVideoProjectFolder(string $videoId, array $selects = ['*']);

    /**
     * Get video details of a specific order
     * 
     * @param string $orderId
     * 
     * @return App\Models\Video\Video
     */
    public function getVideoByOrderId(string $orderId): Video;

    /**
     * Get order by a specific video id
     * 
     * @param string $videoId
     * 
     * @return App\Models\Order\Order
     */
    public function getOrderByVideoId(string $videoId): Order;
}
