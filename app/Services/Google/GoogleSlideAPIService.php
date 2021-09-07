<?php

namespace App\Services\Google;

use App\Utils\GoogleDriveFileUtils;

class GoogleSlideAPIService
{
    private $service;

    public function __construct()
    {
        $client = new \Google_Client();
        $client->setApplicationName('splashr slides');

        // $client->setClientId(Config::get('filesystems.disks.google.clientId'));
        // $client->setClientSecret(Config::get('filesystems.disks.google.clientSecret'));
        // $client->refreshToken(Config::get('filesystems.disks.google.refreshToken'));
        $client->setAuthConfig(GoogleDriveFileUtils::getOAuthCredentialsFile());
        $client->setRedirectUri(env('APP_URL'));
        $client->setScopes(\Google_Service_Slides::PRESENTATIONS_READONLY);

        $this->service = new \Google_Service_Slides($client);
    }

    /**
     * get all slides of a specified presentation
     * 
     * @param string $presentationId
     * @param array $optParams
     * 
     * @return array|null
     */
    public function getSlides(string $presentationId, array $optParams = [
        'fields' => 'slides.objectId'
    ]): ?array
    {
        try {
            $presentation = $this->service->presentations->get($presentationId, $optParams);
            return $presentation->getSlides();
        } catch (\Exception $error) {
            return null;
        }
    }

    /**
     * get a single slide of a specified presentation
     * 
     * @param string $presentationId
     * @param string $pageId
     * @param array $params
     * 
     * @return \Google_Service_Slides_Page|null 
     */
    public function getPresentationPage(string $presentationId, string $pageId, array $optParams = []): ?\Google_Service_Slides_Page
    {
        $params = array_merge([
            'fields' => '*'
        ], $optParams);

        try {
            $page = $this->service->presentations_pages->get($presentationId, $pageId, $params);
            return $page;
        } catch (\Exception $error) {
            return null;
        }
    }

    /**
     * get a single slide of a specified presentation
     * 
     * @param string $presentationId
     * @param string $pageId
     * @param array $params
     * 
     * @return \Google_Service_Slides_Thumbnail|null 
     */
    public function getPresentationPageThumbnail(string $presentationId, string $pageId, array $optParams = []): ?\Google_Service_Slides_Thumbnail
    {
        $params = array_merge([
            'fields' => '*'
        ], $optParams);

        try {
            $thumbnail = $this->service->presentations_pages->getThumbnail($presentationId, $pageId, $params);
            return $thumbnail;
        } catch (\Exception $error) {
            return null;
        }
    }
}
