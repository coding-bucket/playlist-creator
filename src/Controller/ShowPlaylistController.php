<?php

declare(strict_types=1);

namespace PlaylistCreator\Controller;

use PlaylistCreator\Model\Limit;
use PlaylistCreator\Model\Song;
use PlaylistCreator\Service\ORB\ORBStation;
use PlaylistCreator\Util\LimitFilter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

final readonly class ShowPlaylistController
{
    public function __construct(
        private ORBStation $station,
        private OutputInterface $output,
    ) {
    }

    public function list(Limit $timeFrame): int
    {
        try {
            $extractedSongs = $this->station->getAiredSongs($timeFrame->getStart());
            $filter = new LimitFilter($timeFrame);
            /** @var iterable<Song> $songs */
            $songs = $filter->filter($extractedSongs);
            $index = 1;

            foreach ($songs as $song) {
                $this->output->writeln((string) $index.': '.$song->getTimestamp()->toString().' '.$song);
                ++$index;
            }
        } catch (\Exception $e) {
            $this->output->writeln('<error>'.$e->getMessage().'</error>');

            for ($previous = $e->getPrevious(); null !== $previous; $previous = $previous->getPrevious()) {
                $this->output->writeln($previous->getMessage());
                $this->output->writeln($previous->getTraceAsString());
            }

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
