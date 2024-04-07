<?php

declare(strict_types=1);

namespace PlaylistCreator\Model;

use Carbon\Carbon;
use Carbon\CarbonInterval;

final class Limit
{
    /**
     * @throws \InvalidArgumentException
     */
    public function __construct(private Carbon $start, private ?CarbonInterval $interval, private readonly ?int $limit)
    {
        if (null === $interval && null === $limit) {
            throw new \InvalidArgumentException('Limit or interval must be set.');
        }
        if (null !== $limit && $limit <= 0) {
            throw new \InvalidArgumentException('Limit cannot be negative.');
        }
        $this->start = $start->copy();
        $this->interval = $interval?->copy();
    }

    public function getStart(): Carbon
    {
        return $this->start->copy();
    }

    public function isBeforeStart(Carbon $datetime): bool
    {
        return $datetime->isBefore($this->start);
    }

    public function isTimeLimitExceeded(Carbon $datetime): bool
    {
        if (null === $this->interval) {
            return false;
        }

        $end = $this->start->copy()->add($this->interval);

        return $datetime->isAfter($end);
    }

    public function isLimitExceeded(int $i): bool
    {
        return null !== $this->limit && $i > $this->limit;
    }
}
