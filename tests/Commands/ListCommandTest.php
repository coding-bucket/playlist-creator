<?php

declare(strict_types=1);

namespace tests\PlaylistCreator\Commands;

use PHPUnit\Framework\TestCase;
use PlaylistCreator\Commands\ShowCommand;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;

class ListCommandTest extends TestCase
{
    public function testErrorOnStationNotSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "station").');

        $commandTester = new CommandTester(new ShowCommand());
        $commandTester->execute(
            [
                '--date' => '2022-02-02',
                '--limit' => 30,
            ]
        );
    }
}
