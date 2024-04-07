<?php

declare(strict_types=1);

namespace tests\PlaylistCreator\Model;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use PlaylistCreator\Model\Song;

class SongTest extends TestCase
{
    public function testToString(): void
    {
        $song = new Song('Artist', 'Song title', Carbon::now());
        $toString = (string) $song;

        self::assertEquals('"Artist", "Song title"', $toString);
    }
}
