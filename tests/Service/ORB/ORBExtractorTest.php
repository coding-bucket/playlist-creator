<?php

declare(strict_types=1);

namespace tests\PlaylistCreator\Service\ORB;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use PlaylistCreator\Exception\ExtractException;
use PlaylistCreator\Service\ORB\ORBDomExtractor;
use tests\PlaylistCreator\Helper\FileDomProvider;

class ORBExtractorTest extends TestCase
{
    private const testDataDir = __DIR__.'/../../resources/';

    public function testSongs(): void
    {
        $file = self::testDataDir . 'day0.html';
        $fileDomProvider = new FileDomProvider($file);
        $datetime = Carbon::createFromFormat('Y-m-d H:i:s', '2022-06-07 08:00:00');
        /** @var Carbon $datetime */
        $dom = $fileDomProvider->fetchDOM($datetime);
        $songs = (new ORBDomExtractor())->extract($dom, $datetime);

        self::assertEquals(5, count($songs));
    }

    public function testSongsAfterGivenPointInTime(): void
    {
        $file = self::testDataDir . 'day0.html';
        $fileDomProvider = new FileDomProvider($file);
        $datetime = Carbon::createFromFormat('Y-m-d H:i:s', '2022-06-07 09:05:00');
        /** @var Carbon $datetime */
        $dom = $fileDomProvider->fetchDOM($datetime);
        $songs = (new ORBDomExtractor())->extract($dom, $datetime);

        self::assertEquals(4, count($songs));
    }

    public function testSongsWithBrokenDOM(): void
    {
        $this->expectException(ExtractException::class);
        $file = self::testDataDir . 'broken_dom.html';
        $fileDomProvider = new FileDomProvider($file);
        $datetime = Carbon::createFromFormat('Y-m-d H:i:s', '2022-06-07 08:00:00');
        /** @var Carbon $datetime */
        $dom = $fileDomProvider->fetchDOM($datetime);
        $songs = (new ORBDomExtractor())->extract($dom, $datetime);

        self::assertEquals(0, count($songs));
    }

    public function testSongsWithEmptyDOM(): void
    {
        $file = self::testDataDir . 'empty_dom.html';
        $fileDomProvider = new FileDomProvider($file);
        $datetime = Carbon::createFromFormat('Y-m-d H:i:s', '2022-06-07 09:00:00');
        /** @var Carbon $datetime */
        $dom = $fileDomProvider->fetchDOM($datetime);
        $songs = (new ORBDomExtractor())->extract($dom, $datetime);

        self::assertEquals(0, count($songs));
    }
}
