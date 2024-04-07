<?php

declare(strict_types=1);

namespace tests\PlaylistCreator\Service\ORB;

use Carbon\Carbon;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Options;
use PHPUnit\Framework\TestCase;
use PlaylistCreator\Exception\ExtractException;
use PlaylistCreator\Service\ORB\ORBDomProvider;
use PlaylistCreator\Service\ORB\ORBStation;

class ORBStationTest extends TestCase
{
    private const testDataDir = __DIR__ . '/../../resources/';

    /**
     * @throws ExtractException
     */
    public function testGetAiredSongs(): void
    {
        $stub = $this->createStub(ORBDomProvider::class);

        /** @var Carbon $datetime */
        $datetime = Carbon::createFromFormat('Y-m-d H:i:s', '2022-06-07 08:00:00');

        $map = [
            $this->getDom('day0.html'),
            $this->getDom('day1.html'),
            $this->getDom('day2.html'),
        ];

        $stub->method('fetchDOM')->will(self::onConsecutiveCalls($map[0], $map[1], $map[2]));

        $station = new ORBStation('test', $stub);
        $songs = $station->getAiredSongs($datetime);
        $limit = 0;
        foreach ($songs as $song) {
            ++$limit;
            if (15 === $limit) {
                break;
            }
        }

        self::assertEquals(15, $limit);
    }

    private function getDom(string $filename): Dom
    {
        $file = self::testDataDir . $filename;
        $dom = new Dom();
        $dom->loadFromFile($file, (new Options())->setWhitespaceTextNode(false));

        return $dom;
    }
}
