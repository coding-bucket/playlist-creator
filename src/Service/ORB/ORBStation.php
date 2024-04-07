<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

declare(strict_types=1);

namespace PlaylistCreator\Service\ORB;

use Carbon\Carbon;
use PlaylistCreator\Exception\ExtractException;
use PlaylistCreator\Model\Song;

final class ORBStation
{
    public const MAX_EMPTY_RESPONSES = 3;

    public function __construct(public readonly string $name, private readonly ORBDomProvider $domProvider)
    {
    }

    public function getPlaylistPrefix(): string
    {
        return $this->name;
    }

    /**
     * @return \Generator<Song>
     *
     * @throws \PlaylistCreator\Exception\ExtractException
     */
    public function getAiredSongs(Carbon $start): \Generator
    {
        $now = Carbon::now();
        $emptyResponses = 0;
        $orbExtractor = new ORBDomExtractor();

        while ($start->lessThan($now)) {
            if (self::MAX_EMPTY_RESPONSES === $emptyResponses) {
                // stop pulling playlists, if the pages load successfully, but no songs are found
                throw new ExtractException('Error fetching songs: Too many empty pages.');
            }

            $dom = $this->domProvider->fetchDOM($start);
            $dailySongs = $orbExtractor->extract($dom, $start);

            if (0 === count($dailySongs)) {
                ++$emptyResponses;
            }

            foreach ($dailySongs as $song) {
                yield $song;
            }

            $start = $start->addDay()->setTime(0, 0);

        }
    }
}
