<?php

declare(strict_types=1);

namespace tests\PlaylistCreator\Commands;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use PlaylistCreator\Commands\Option\StartOptions;
use Symfony\Component\Console\Command\Command;

class StartOptionsTest extends TestCase
{
    protected object $trait;

    protected function setUp(): void
    {
        $this->trait = new class() {
            use StartOptions;

            public function addOption(
                ?string $name,
                mixed $shortcut = null,
                ?int $mode = null,
                string $description = '',
                mixed $default = null
            ): Command {
                return $this->addOption($name, $shortcut, $mode, $description, $default);
            }
        };
    }

    /**
     * @dataProvider validDatesProvider
     */
    public function testGetCarbonDate(string $date, string $time, string $expected): void
    {
        /** @var Carbon $carbon */
        $carbon = $this->trait->getCarbonDate($date, $time); /* @phpstan-ignore-line */

        self::assertEquals($expected, $carbon->format('Y-m-d H:i'));
    }

    /**
     * @return array<array<string>>
     */
    public function validDatesProvider(): array
    {
        return [
            ['2022-03-01', '09:00', '2022-03-01 09:00'],
        ];
    }

    /**
     * @dataProvider invalidDatesProvider
     */
    public function testGetCarbonDateThrowsException(string $date, string $time): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->trait->getCarbonDate($date, $time); /* @phpstan-ignore-line */
    }

    /**
     * @return array<array<string>>
     */
    public function invalidDatesProvider(): array
    {
        return [
            // invalid date
            ['2022-03-33', '09:00'],

            // invalid time
            ['2022-03-01', '9:0'],
            ['2022-03-01', '11a:00'],
            ['2022-03-01', '25:00'],
            ['2022-03-01', '02:60'],

            ['2022-03-01', '2:0'],
            ['2022-03-01', '9:00'],
            ['2022-03-01', '0:00'],
            ['2022-3-3',  '09:00'],
        ];
    }
}
