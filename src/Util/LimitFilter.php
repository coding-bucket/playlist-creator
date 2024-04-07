<?php

declare(strict_types=1);

namespace PlaylistCreator\Util;

use Exception;
use PlaylistCreator\Interface\LimitFilterable;
use PlaylistCreator\Model\Limit;

final readonly class LimitFilter
{
    public function __construct(private Limit $timeFrame)
    {
    }

    /**
     * @param \Generator<LimitFilterable> $songs
     *
     * @return \Generator<LimitFilterable>
     */
    public function filter(\Generator $songs): \Generator
    {
        $yielded = 1;

        foreach ($songs as $song) {
            if($song instanceof Exception) {
                yield $song;
                break;
            }

            if ($this->timeFrame->isLimitExceeded($yielded)) {
                break;
            }

            $date = $song->getTimestamp();

            if ($this->timeFrame->isBeforeStart($date)) {
                continue;
            }

            if ($this->timeFrame->isTimeLimitExceeded($date)) {
                break;
            }

            yield $song;
            ++$yielded;
        }
    }
}
