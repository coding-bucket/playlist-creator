<?php

declare(strict_types=1);

namespace PlaylistCreator\Commands;

use PlaylistCreator\Commands\Argument\StationArgument;
use PlaylistCreator\Commands\Option\LimiterOptions;
use PlaylistCreator\Service\ORB\ORBService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class StationsCommand extends Command
{
    use StationArgument;
    use LimiterOptions;

    /**
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure(): void
    {
        $this->setName('stations')
            ->setDescription('Lists the supported radio stations.')
            ->setHelp('Lists the supported radio stations.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $stations = ORBService::getStationNames();
        } catch (\RuntimeException $e) {
            $output->writeln('<error>'.$e->getMessage().'</error>');

            return Command::FAILURE;
        }

        $output->writeln($this->asTextList($stations));

        return Command::SUCCESS;
    }

    /**
     * @param array<string, string> $stations
     */
    private function asTextList(array $stations): string
    {
        $listing = '';
        foreach ($stations as $key => $station) {
            $listing .= $key.' - '.$station.PHP_EOL;
        }

        return $listing;
    }
}
