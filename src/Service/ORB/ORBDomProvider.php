<?php

declare(strict_types=1);

namespace PlaylistCreator\Service\ORB;

use Carbon\Carbon;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Options;
use PlaylistCreator\Exception\ExtractException;
use PlaylistCreator\Interface\DomProvider;
use Psr\Http\Client\ClientExceptionInterface;

class ORBDomProvider implements DomProvider
{
    private string $playlist_uri;

    public function __construct(string $station_id)
    {
        $this->playlist_uri = 'https://onlineradiobox.com/de/'.$station_id.'/playlist/';
    }

    /**
     * @throws ExtractException
     */
    public function fetchDOM(Carbon $datetime): Dom
    {
        $diffInDays = $datetime->copy()->startOfDay()->diffInDays(Carbon::now()->startOfDay());

        if ($diffInDays > 6) {
            throw new ExtractException('ORB playlists are only available for the last 6 days');
        }

        $uri = $diffInDays > 0 ? $this->playlist_uri.$diffInDays : $this->playlist_uri;
        $dom = new Dom();

        try {
            $dom->loadFromUrl($uri, (new Options())->setWhitespaceTextNode(false));
        } catch (ClientExceptionInterface $e) {
            throw new ExtractException(message: 'HTTP client error: ', previous: $e);
        } catch (\Throwable $e) {
            throw new ExtractException(message: 'DOM loading error: ', previous: $e);
        }

        return $dom;
    }
}
