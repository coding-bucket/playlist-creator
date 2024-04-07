<?php

declare(strict_types=1);

namespace tests\PlaylistCreator\Model;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use PHPUnit\Framework\TestCase;
use PlaylistCreator\Model\Limit;

class LimitTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testConstructWithIntervalAndLimit(): void
    {
        $startDateString = '2022-10-03 12:23:00';
        $startDate = Carbon::createFromFormat('Y-m-d G:i:s', $startDateString);
        assert(false !== $startDate);
        $interval = new CarbonInterval(0, 0, 0, 0, 3);
        $count = 10;
        $limit = new Limit($startDate, $interval, $count);
        $start = $limit->getStart();

        self::assertEquals(
            $startDate->toString(),
            $start->toString(),
            'start of time frame set correctly'
        );
    }

    public function testConstructWithoutIntervalAndLimit(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $startDateString = '2022-10-03 12:23:00';
        $startDate = Carbon::createFromFormat('Y-m-d G:i:s', $startDateString);
        assert(false !== $startDate);
        new Limit($startDate, null, null);
    }

    public function testConstructWithNegativeLimit(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $startDateString = '2022-10-03 12:23:00';
        $startDate = Carbon::createFromFormat('Y-m-d G:i:s', $startDateString);
        assert(false !== $startDate);
        new Limit($startDate, null, -10);
    }

    /**
     * @throws \Exception
     */
    public function testIsTimeLimitExceeded(): void
    {
        $startDateString = '2022-10-03 12:23:00';
        $startDate = Carbon::createFromFormat('Y-m-d G:i:s', $startDateString);
        assert(false !== $startDate);
        $interval = new CarbonInterval(0, 0, 0, 0, 3);
        $beforeIntervalDate = $startDate->copy()->subHour();
        $inIntervalDate = $startDate->copy()->addHour();
        $afterIntervalDate = $startDate->copy()->add($interval)->addHour();
        $limit = new Limit($startDate, $interval, null);

        self::assertFalse(
            $limit->isTimeLimitExceeded($beforeIntervalDate),
            'date before start date is not limited by time frame'
        );

        self::assertFalse(
            $limit->isTimeLimitExceeded($inIntervalDate),
            'date in interval is not limited by time frame'
        );

        self::assertTrue(
            $limit->isTimeLimitExceeded($afterIntervalDate),
            'date after end is limited by time frame'
        );
    }

    /**
     * @throws \Exception
     */
    public function testIsBeforeStart(): void
    {
        $startDateString = '2022-10-03 12:23:00';
        $startDate = Carbon::createFromFormat('Y-m-d G:i:s', $startDateString);
        assert(false !== $startDate);
        $interval = new CarbonInterval(0, 0, 0, 0, 3);
        $beforeIntervalDate = $startDate->copy()->subHour();
        $inIntervalDate = $startDate->copy()->addHour();
        $afterIntervalDate = $startDate->copy()->add($interval)->addHour();
        $limit = new Limit($startDate, $interval, null);

        self::assertTrue(
            $limit->isBeforeStart($beforeIntervalDate),
            'date before start date is before time frame'
        );

        self::assertFalse(
            $limit->isBeforeStart($inIntervalDate),
            'date in interval is not before time frame'
        );

        self::assertFalse(
            $limit->isBeforeStart($afterIntervalDate),
            'date after end date is not before time frame'
        );
    }

    /**
     * @throws \Exception
     */
    public function testIsLimitExceeded(): void
    {
        $startDateString = '2022-10-03 12:23:00';
        $startDate = Carbon::createFromFormat('Y-m-d G:i:s', $startDateString);
        assert(false !== $startDate);
        $interval = new CarbonInterval(0, 0, 0, 0, 3);
        $timeframeWithPositiveLimit = new Limit($startDate, $interval, 15);

        self::assertTrue(
            $timeframeWithPositiveLimit->isLimitExceeded(20),
            '20 is above limit of 15'
        );

        self::assertFalse(
            $timeframeWithPositiveLimit->isLimitExceeded(15),
            '15 is not above limit of 15'
        );

        self::assertFalse(
            $timeframeWithPositiveLimit->isLimitExceeded(10),
            '10 is not above limit of 15'
        );
    }
}
