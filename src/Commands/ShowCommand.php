<?php

declare(strict_types=1);

namespace PlaylistCreator\Commands;

use PlaylistCreator\Commands\Argument\StationArgument;
use PlaylistCreator\Commands\Option\LimiterOptions;
use PlaylistCreator\Commands\Option\StartOptions;
use PlaylistCreator\Controller\ShowPlaylistController;
use PlaylistCreator\Model\Limit;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ShowCommand extends Command
{
    use StationArgument;
    use LimiterOptions;
    use StartOptions;

    /**
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure(): void
    {
        $this->setName('show')
            ->setDescription('Shows the playlist of aired songs from supported radio stations.')
            ->setHelp('Shows the playlist of aired songs from supported radio stations.')
            ->addStationArgument()
            ->addLimiterOptions()
            ->addStartOptions();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $station = $this->getStation($input);
            $start = $this->getStartTimestamp($input);
            [$limiter, $duration] = $this->getLimitAndDuration($input);
            $limit = new Limit($start, $duration, $limiter);
        } catch (\InvalidArgumentException $e) {
            $output->writeln('<error>'.$e->getMessage().'</error>');

            return Command::FAILURE;
        }

        $listController = new ShowPlaylistController($station, $output);

        return $listController->list($limit);
    }
}
