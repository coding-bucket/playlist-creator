<?php

declare(strict_types=1);

namespace PlaylistCreator\Controller;

use PlaylistCreator\Interface\Client;
use PlaylistCreator\Model\PlaylistIdentifier;
use Symfony\Component\Console\Output\OutputInterface;

final readonly class PlaylistManager
{
    public function __construct(private Client $youtubeClient, private OutputInterface $output)
    {
    }

    public function resolveOrCreatePlaylist(PlaylistIdentifier $playlistOptions, string $defaultTitle): string|null
    {
        $id = $playlistOptions->id;

        if (null !== $id) {
            return $this->resolvePlaylist($id);
        }

        $title = $playlistOptions->title ?? $defaultTitle;
        $this->output->writeln('Creating playlist "' . $title . '"');

        return $this->createPlaylist($title);
    }

    public function resolvePlaylist(string $id): ?string
    {
        try {
            if ($this->youtubeClient->hasPlaylist($id)) {
                return $id;
            }
        } catch (\Exception $e) {
            $this->output->writeln(' -- <fg=red>error retrieving playlist</>');
            return null;
        }

        $this->output->writeln(' -- <fg=red>playlist id not found</>');
        return null;
    }

    public function createPlaylist(string $title): ?string
    {
        try {
            return $this->youtubeClient->addPlaylist($title);
        } catch (\Exception $e) {
            $this->output->writeln(' -- <fg=red>error creating playlist</>');
            return null;
        }
    }
}
