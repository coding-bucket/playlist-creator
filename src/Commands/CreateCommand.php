<?php

declare(strict_types=1);

namespace PlaylistCreator\Commands;

use PlaylistCreator\Commands\Argument\StationArgument;
use PlaylistCreator\Commands\Option\InteractiveOption;
use PlaylistCreator\Commands\Option\LimiterOptions;
use PlaylistCreator\Commands\Option\PlaylistOptions;
use PlaylistCreator\Commands\Option\StartOptions;
use PlaylistCreator\Controller\CreatePlaylistController;
use PlaylistCreator\Model\Limit;
use PlaylistCreator\Service\Youtube\YoutubeClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateCommand extends Command
{
    use StationArgument;
    use LimiterOptions;
    use StartOptions;
    use PlaylistOptions;
    use InteractiveOption;

    /**
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure(): void
    {
        $this->setName('create')
            ->setDescription('Creates a Youtube playlist with songs broadcast from supported radio stations.')
            ->setHelp('Creates a Youtube playlist with songs broadcast from supported radio stations.')
            ->addStationArgument()
            ->addLimiterOptions()
            ->addStartOptions()
            ->addPlaylistOptions()
            ->addInteractiveOption();
    }

    /**
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $station = $this->getStation($input);
            $start = $this->getStartTimestamp($input);
            [$limiter, $duration] = $this->getLimitAndDuration($input);
            $playlistOptions = $this->getPlaylistOptions($input);
            $interactive = $this->isInteractive($input);
            $limit = new Limit($start, $duration, $limiter);
        } catch (\InvalidArgumentException $e) {
            $output->writeln('<error>'.$e->getMessage().'</error>');

            return Command::FAILURE;
        }

        $youtubeClient = new YoutubeClient();

        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');
        $createController = new CreatePlaylistController($youtubeClient, $station, $input, $output, $helper);

        return $createController->createPlaylist($limit, $playlistOptions, $interactive);
    }
}
