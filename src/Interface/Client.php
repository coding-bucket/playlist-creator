<?php

declare(strict_types=1);

namespace PlaylistCreator\Interface;

use PlaylistCreator\Model\SearchResult;

interface Client
{
    /**
     * @throws \PlaylistCreator\Exception\YoutubeException
     */
    public function searchVideoId(string $searchText): ?SearchResult;

    /**
     * @throws \PlaylistCreator\Exception\YoutubeException
     */
    public function insertInPlaylist(string $videoId, string $playlistId): void;

    /**
     * @throws \PlaylistCreator\Exception\YoutubeException
     */
    public function addPlaylist(string $title): string;

    /**
     * @throws \PlaylistCreator\Exception\YoutubeException
     */
    public function hasPlaylist(string $id): bool;
}
