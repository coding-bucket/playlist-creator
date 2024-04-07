<?php

declare(strict_types=1);

namespace tests\PlaylistCreator\Commands;

use PHPUnit\Framework\TestCase;
use PlaylistCreator\Commands\CreateCommand;
use Symfony\Component\Console\Tester\CommandTester;

class CreateCommandTest extends TestCase
{
    /**
     * @throws \RuntimeException
     */
    public function testErrorOnPlaylistIDAndTitleSet(): void
    {
        $createCommand = new CreateCommand();
        $commandTester = new CommandTester($createCommand);
        $commandTester->execute(
            [
                'station' => 'wdr4',
                '--date' => '2023-03-03',
                '--limit' => 6,
                '--id' => 'abc',
                '--title' => 'title',
            ]
        );
        $output = $commandTester->getDisplay();

        self::assertStringContainsString($createCommand::ERROR_PLAYLIST_ID_AND_TITLE_SET, $output);
    }

    public function testErrorOnStationNotExists(): void
    {
        $createCommand = new CreateCommand();
        $commandTester = new CommandTester($createCommand);
        $unsupported_station = 'not_supported_station';
        $commandTester->execute(
            [
                'station' => $unsupported_station,
                '--date' => '2023-03-03',
                '--limit' => 6,
            ]
        );
        $output = $commandTester->getDisplay();
        $message = sprintf($createCommand::ERROR_STATION_NOT_SUPPORTED, $unsupported_station);

        self::assertStringContainsString($message, $output);
    }

    public function testErrorOnLimitZero(): void
    {
        $createCommand = new CreateCommand();
        $commandTester = new CommandTester($createCommand);
        $commandTester->execute(
            [
                'station' => 'wdr4',
                '--date' => '2023-03-03',
                '--limit' => 0,
            ]
        );
        $output = $commandTester->getDisplay();

        self::assertStringContainsString($createCommand::ERROR_LIMIT_FORMAT, $output);
    }

    public function testErrorOnInvalidLimit(): void
    {
        $createCommand = new CreateCommand();
        $commandTester = new CommandTester($createCommand);
        $commandTester->execute(
            [
                'station' => 'wdr4',
                '--date' => '2023-03-03',
                '--limit' => -10, // todo: check 2.3 loops
            ]
        );
        $output = $commandTester->getDisplay();

        self::assertStringContainsString($createCommand::ERROR_LIMIT_FORMAT, $output);
    }

    public function testErrorOnInvalidDuration(): void
    {
        $createCommand = new CreateCommand();
        $commandTester = new CommandTester($createCommand);
        $commandTester->execute(
            [
                'station' => 'wdr4',
                '--date' => '2023-03-03',
                '--duration' => '3',
            ]
        );
        $output = $commandTester->getDisplay();

        self::assertStringContainsString($createCommand::ERROR_DURATION_FORMAT, $output);
    }

    public function testErrorOnZeroDuration(): void
    {
        $createCommand = new CreateCommand();
        $commandTester = new CommandTester($createCommand);
        $commandTester->execute(
            [
                'station' => 'wdr4',
                '--date' => '2023-03-03',
                '--duration' => '0:00',
            ]
        );
        $output = $commandTester->getDisplay();

        self::assertStringContainsString($createCommand::ERROR_DURATION_LENGTH, $output);
    }

    public function testErrorOnLimitOrDurationMustBeSet(): void
    {
        $createCommand = new CreateCommand();
        $commandTester = new CommandTester($createCommand);
        $commandTester->execute(
            [
                'station' => 'wdr4',
                '--date' => '2023-03-03',
            ]
        );
        $output = $commandTester->getDisplay();

        self::assertStringContainsString($createCommand::ERROR_LIMIT_AND_DURATION, $output);
    }

    public function testErrorOnWrongDateFormat(): void
    {
        $createCommand = new CreateCommand();
        $commandTester = new CommandTester($createCommand);
        $commandTester->execute(
            [
                'station' => 'wdr4',
                '--date' => '2023-03',
                '--limit' => 6,
            ]
        );
        $output = $commandTester->getDisplay();

        self::assertStringContainsString($createCommand::ERROR_DATE_TIME_FORMAT, $output);
    }

    public function testErrorOnWrongTimeFormat(): void
    {
        $createCommand = new CreateCommand();
        $commandTester = new CommandTester($createCommand);
        $commandTester->execute(
            [
                'station' => 'wdr4',
                '--date' => '2023-03-03',
                '--time' => '12',
                '--limit' => 6,
            ]
        );
        $output = $commandTester->getDisplay();

        self::assertStringContainsString($createCommand::ERROR_DATE_TIME_FORMAT, $output);
    }

    public function testErrorOnInvalidDateOrTime(): void
    {
        $createCommand = new CreateCommand();
        $commandTester = new CommandTester($createCommand);
        $commandTester->execute(
            [
                'station' => 'wdr4',
                '--date' => '2023-13-01',
                '--time' => '12:99',
                '--limit' => 6,
            ]
        );
        $output = $commandTester->getDisplay();

        self::assertStringContainsString($createCommand::ERROR_DATE_TIME_INVALID, $output);
    }
}
