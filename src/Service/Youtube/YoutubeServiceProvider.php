<?php

declare(strict_types=1);

namespace PlaylistCreator\Service\Youtube;

use Google\Exception;
use Google\Service\YouTube\Resource\PlaylistItems;
use Google\Service\YouTube\Resource\Playlists;
use Google\Service\YouTube\Resource\Search;

final class YoutubeServiceProvider
{
    private \Google_Service_YouTube $apiKeyService;
    private \Google_Service_YouTube $oAuthService;

    private static string $RESOURCE_DIR = __DIR__.'/../../../bin/';

    /**
     * @throws \RuntimeException
     * @throws Exception
     */
    public function __construct()
    {
        $this->apiKeyService = $this->createServiceByApplicationKey();
        $this->oAuthService = $this->createServiceByAuthURL();
    }

    public function getSearchService(): Search
    {
        return $this->apiKeyService->search;
    }

    public function getPlaylistService(): Playlists
    {
        return $this->oAuthService->playlists;
    }

    public function getPlaylistItemsService(): PlaylistItems
    {
        return $this->oAuthService->playlistItems;
    }

    private function createServiceByApplicationKey(): \Google_Service_YouTube
    {
        if (!file_exists(self::$RESOURCE_DIR.'api-key.php')) {
            throw new \RuntimeException('API key file not found at bin/api-key.php.');
        }

        $apiKey = include self::$RESOURCE_DIR.'api-key.php';
        $apiClient = new \Google_Client();
        $apiClient->setApplicationName('Youtube Playlist Creator');
        $apiClient->setDeveloperKey($apiKey);
        $this->apiKeyService = new \Google_Service_YouTube($apiClient);

        return $this->apiKeyService;
    }

    /**
     * @throws \InvalidArgumentException
     * @throws Exception
     */
    private function createServiceByAuthURL(): \Google_Service_YouTube
    {
        if (!file_exists(self::$RESOURCE_DIR.'client_secret.json')) {
            throw new \RuntimeException('Client secret file not found at bin/client_secret.json');
        }

        $client = new \Google_Client();
        $client->setApplicationName('Youtube Playlist Creator');
        $client->setScopes(['https://www.googleapis.com/auth/youtube.force-ssl']);
        $client->setAuthConfig(self::$RESOURCE_DIR.'client_secret.json');
        $client->setAccessType('offline');

        $authUrl = $client->createAuthUrl();
        printf('Open this link in your browser and follow instructions:'
            . PHP_EOL . '%s' . PHP_EOL .'Enter verification code: ', $authUrl);

        $authCode = trim((string) fgets(STDIN));

        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
        $client->setAccessToken($accessToken);
        $this->oAuthService = new \Google_Service_YouTube($client);

        return $this->oAuthService;
    }
}
