<?php

declare(strict_types=1);

namespace tests\PlaylistCreator\Util;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use PHPUnit\Framework\TestCase;
use PlaylistCreator\Model\Limit;
use PlaylistCreator\Model\Song;
use PlaylistCreator\Util\LimitFilter;

class LimitFilterTest extends TestCase
{
    private function getSongFixtures(): \Generator
    {
        // Create 100 songs with 3 minutes interval
        for ($i = 0; $i < 100; ++$i) {
            /** @var Carbon $date */
            $date = Carbon::createFromFormat('Y-m-d G:i:s', '2024-01-03 12:00:00');
            $date->addMinutes(3 * $i);
            yield new Song('artist '.(string) $i, 'title '.(string) $i, $date);
        }
    }

    public function testFilterWithNumberLimitSet(): void
    {
        /** @var Carbon $start */
        $start = Carbon::createFromFormat('Y-m-d G:i:s', '2024-01-03 12:23:00');
        $frame = new Limit($start, null, 15);
        $filter = new LimitFilter($frame);
        /** @var Song[] $filteredSongs */
        $filteredSongs = iterator_to_array($filter->filter($this->getSongFixtures()));

        self::assertEquals(15, count($filteredSongs));
        self::assertEquals('"artist 22", "title 22"', (string) end($filteredSongs));
    }

    public function testFilterWithIntervalLimitSet(): void
    {
        /** @var Carbon $start */
        $start = Carbon::createFromFormat('Y-m-d G:i:s', '2024-01-03 12:23:00');
        $interval = CarbonInterval::create(0, 0, 0, 0, 2);
        $frame = new Limit($start, $interval, null);
        $filter = new LimitFilter($frame);
        /** @var Song[] $filteredSongs */
        $filteredSongs = iterator_to_array($filter->filter($this->getSongFixtures()));
        $count = count(iterator_to_array($filteredSongs));

        self::assertEquals(40, $count);
        self::assertEquals('"artist 47", "title 47"', (string) end($filteredSongs));
    }
}
