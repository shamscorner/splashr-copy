<?php

namespace App\Utils;

class GoogleDriveFileUtils
{
    public const TYPE_FONTS = 'fonts';
    public const TYPE_LOGOS = 'logos';
    public const TYPE_VIDEOS = 'videos';
    public const TYPE_GRAPHIC_CHARTERS = 'graphic-charters';
    public const TYPE_SOUNDS = 'sounds';

    public const FOLDER_FONTS = 'Fonts';
    public const FOLDER_LOGOS = 'Logos';
    public const FOLDER_VIDEOS = 'Videos';
    public const FOLDER_GRAPHIC_CHARTERS = 'Graphic Charters';
    public const FOLDER_SOUNDS = 'Sounds';
    public const FOLDER_ASSETS = 'assets';
    public const FOLDER_CREATIVE_IDEAS = 'Creative Ideas';

    public const PROJECT_FOLDERS = [
        self::FOLDER_FONTS,
        self::FOLDER_LOGOS,
        self::FOLDER_VIDEOS,
        self::FOLDER_GRAPHIC_CHARTERS,
        self::FOLDER_SOUNDS,
        self::FOLDER_ASSETS,
        self::FOLDER_CREATIVE_IDEAS
    ];

    public static function getFilteredQuery($type): array
    {
        switch ($type) {
            case self::TYPE_LOGOS:
                return [
                    'operator' => 'contains',
                    'value' => "image/' or mimeType contains 'application/postscript"
                ];
            case self::TYPE_FONTS:
                return [
                    'operator' => 'contains',
                    'value' => 'x-font'
                ];
            case self::TYPE_GRAPHIC_CHARTERS:
                return [
                    'operator' => 'contains',
                    'value' => 'pdf'
                ];
            case self::TYPE_SOUNDS:
                return [
                    'operator' => 'contains',
                    'value' => 'audio'
                ];
            default:
                return [];
        }
    }

    public static function getOAuthCredentialsFile()
    {
        // oauth2 creds
        $oauth_creds = __DIR__ . '/../../splashr-app.json';

        if (file_exists($oauth_creds)) {
            return $oauth_creds;
        }

        return false;
    }
}
