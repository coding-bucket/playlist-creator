<?php

declare(strict_types=1);

namespace PlaylistCreator\Controller;

use Generator;
use PlaylistCreator\Exception\ExtractException;
use PlaylistCreator\Interface\Client;
use PlaylistCreator\Model\Limit;
use PlaylistCreator\Model\PlaylistIdentifier;
use PlaylistCreator\Model\Song;
use PlaylistCreator\Service\ORB\ORBStation;
use PlaylistCreator\Util\LimitFilter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final readonly class CreatePlaylistController
{
    public function __construct(
        private Client          $youtubeClient,
        private ORBStation      $station,
        private InputInterface  $input,
        private OutputInterface $output,
        private QuestionHelper  $helper
    )
    {
    }

    public function createPlaylist(Limit $timeFrame, PlaylistIdentifier $playlistOptions, bool $interactive): int
    {
        $songsAdded = 0;
        $extractedSongs = $this->getExtractedSongs($timeFrame);
        $defaultTitle = $this->station->getPlaylistPrefix() . ' playlist ' .
            $timeFrame->getStart()->format('Y-m-d H:i:s');

        $playlistManager = new PlaylistManager($this->youtubeClient, $this->output);
        $playlistId = $playlistManager->resolveOrCreatePlaylist($playlistOptions, $defaultTitle);

        if (null === $playlistId) {
            return Command::FAILURE;
        }

        $songManager = new SongManager($this->youtubeClient, $this->input, $this->output, $this->helper);

        try {
            foreach ($extractedSongs as $song) {
                $isAdded = $songManager->addToPlaylist($song, $playlistId, $interactive);
                $songsAdded += $isAdded ? 1 : 0;
            }
        }catch(ExtractException $e){
            $this->output->writeln('Error fetching songs: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $this->output->writeln(
            PHP_EOL . (string)$songsAdded . ' songs added to playlist.' . PHP_EOL
            . 'https://www.youtube.com/playlist?list=' . $playlistId
        );

        return Command::SUCCESS;
    }

    /**
     * @return Generator<Song>
     *
     * @throws \PlaylistCreator\Exception\ExtractException
     */
    private function getExtractedSongs(Limit $limit): Generator
    {
        $extractedSongs = $this->station->getAiredSongs($limit->getStart());
        $filter = new LimitFilter($limit);

        /** @var iterable<Song> $songs */
        $songs = $filter->filter($extractedSongs);

        yield from $songs;
    }
}
