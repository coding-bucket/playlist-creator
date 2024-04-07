<?php

declare(strict_types=1);

namespace PlaylistCreator\Commands\Option;

use Carbon\CarbonInterval;
use Carbon\Exceptions\ParseErrorException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

trait LimiterOptions
{
    public const ERROR_LIMIT_AND_DURATION = 'Either duration or limit must be set.';
    public const ERROR_LIMIT_FORMAT = 'Limit must be a positive integer.';
    public const ERROR_DURATION_FORMAT = 'Duration must be in format G:i, eg. "3:00".';
    public const ERROR_DURATION_LENGTH = 'Duration must be at least 1 minute.';

    private const OPTION_NAME_LIMIT = 'limit';
    private const OPTION_NAME_DURATION = 'duration';

    private const OPTION_DESC_LIMIT = 'The number of songs to be added to the playlist. Either duration or limit must'
        .'be set.';
    private const OPTION_DESC_DURATION = 'The duration of the time frame of the broadcast songs to be added to '
        .'the playlist. Either duration or limit must be set.';

    abstract public function addOption(
        string $name,
        mixed $shortcut = null,
        ?int $mode = null,
        string $description = '',
        mixed $default = null
    );

    /**
     * @return array{0: int|null, 1: CarbonInterval|null}
     *
     * @throws \InvalidArgumentException
     */
    public function getLimitAndDuration(InputInterface $input): array
    {
        $limit = $this->getLimit($input);
        $duration = $this->getDuration($input);

        if (null === $limit && null === $duration) {
            throw new \InvalidArgumentException(self::ERROR_LIMIT_AND_DURATION);
        }

        return [$limit, $duration];
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function getLimit(InputInterface $input): ?int
    {
        $limit = $input->getOption(self::OPTION_NAME_LIMIT);

        if (null === $limit) {
            return null;
        }

        if (!is_numeric($limit) || (int) $limit <= 0) {
            throw new \InvalidArgumentException(self::ERROR_LIMIT_FORMAT);
        }

        return (int) $limit;
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function getDuration(InputInterface $input): ?CarbonInterval
    {
        $duration = $input->getOption(self::OPTION_NAME_DURATION);

        if (null === $duration) {
            return null;
        }

        try {
            $interval = CarbonInterval::createFromFormat('G:i', (string) $duration);
        } catch (ParseErrorException) {
            throw new \InvalidArgumentException(self::ERROR_DURATION_FORMAT);
        }

        if (0 === (int) $interval->totalMinutes) {
            throw new \InvalidArgumentException(self::ERROR_DURATION_LENGTH);
        }

        return $interval;
    }

    /**
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    private function addLimiterOptions(): self
    {
        $this->addOption(self::OPTION_NAME_LIMIT, 'c', InputOption::VALUE_REQUIRED, self::OPTION_DESC_LIMIT)
            ->addOption(self::OPTION_NAME_DURATION, 'u', InputOption::VALUE_REQUIRED, self::OPTION_DESC_DURATION);

        return $this;
    }
}
