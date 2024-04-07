<?php

declare(strict_types=1);

namespace PlaylistCreator\Commands\Option;

use Carbon\Carbon;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

trait StartOptions
{
    public const ERROR_DATE_TIME_FORMAT = 'Invalid format for options date (Y-m-d, e.g. "2023-02-28") '.
        'and/or time (H:i, e.g. "08:00").';
    public const ERROR_DATE_TIME_INVALID = 'Invalid date and/or time.';

    private const OPTION_NAME_DATE = 'date';
    private const OPTION_NAME_TIME = 'time';

    private const OPTION_DESC_DATE = 'The date of the songs aired (format Y-m-d, eg. "2022-03-09", default: today)';
    private const OPTION_DESC_TIME = 'The time of the songs aired (format H:i, eg "13:00", default: 00:00).';

    abstract public function addOption(
        string $name,
        mixed $shortcut = null,
        ?int $mode = null,
        string $description = '',
        mixed $default = null
    );

    /**
     * @throws \InvalidArgumentException
     */
    public function getStartTimestamp(InputInterface $input): Carbon
    {
        $date = (string) $input->getOption(self::OPTION_NAME_DATE);
        $time = (string) $input->getOption(self::OPTION_NAME_TIME);

        return $this->getCarbonDate($date, $time);
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function getCarbonDate(string $date, string $time): Carbon
    {
        if ('' === $time) {
            $time = '00:00';
        }

        if ('' === $date) {
            $date = Carbon::now()->format('Y-m-d');
        }

        try {
            $datetime = Carbon::createFromFormat('Y-m-d H:i', $date.' '.$time);
        } catch (\Exception) {
            throw new \InvalidArgumentException(self::ERROR_DATE_TIME_FORMAT);
        }

        if (false === $datetime) {
            throw new \InvalidArgumentException(self::ERROR_DATE_TIME_FORMAT);
        }

        if ($date !== $datetime->format('Y-m-d') || $time !== $datetime->format('H:i')) {
            throw new \InvalidArgumentException(self::ERROR_DATE_TIME_INVALID);
        }

        return $datetime;
    }

    /**
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    private function addStartOptions(): self
    {
        $this->addOption(
            self::OPTION_NAME_DATE,
            'd',
            InputOption::VALUE_REQUIRED,
            self::OPTION_DESC_DATE,
            date('Y-m-d')
        )->addOption(self::OPTION_NAME_TIME, 't', InputOption::VALUE_REQUIRED, self::OPTION_DESC_TIME, '00:00');

        return $this;
    }
}
