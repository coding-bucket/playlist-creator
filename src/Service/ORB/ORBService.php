<?php

declare(strict_types=1);

namespace PlaylistCreator\Service\ORB;

final class ORBService
{
    /**
     * @var array<string, array<string, string>>|null
     */
    private static ?array $registry = null;

    /**
     * @return array<string, string>
     */
    public static function getStationNames(): array
    {
        $callable = static fn (array $array): string => $array['name'];

        return array_map($callable, self::getStationsFromFile());
    }

    public function getStation(string $stationName): ORBStation
    {
        $registry = self::getStationsFromFile();

        $path = $registry[$stationName]['path'] ?? null;
        $name = $registry[$stationName]['name'] ?? null;

        if (null === $path || null === $name) {
            throw new \InvalidArgumentException('Station not supported.');
        }

        $orbDomProvider = new ORBDomProvider($path);
        return new ORBStation($name, $orbDomProvider);
    }

    /**
     * @return array<string, array<string, string>>
     */
    private static function getStationsFromFile(): array
    {
        if (null === self::$registry) {
            $jsonString = file_get_contents(__DIR__.'/../../../bin/stations.json');
            if (false === $jsonString) {
                throw new \RuntimeException('Could not read stations.json');
            }
            try {
                self::$registry = json_decode($jsonString, true, 5, JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                throw new \RuntimeException('Could not parse stations.json');
            }
        }

        return self::$registry;
    }
}
