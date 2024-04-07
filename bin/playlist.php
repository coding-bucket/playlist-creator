<?php

declare(strict_types=1);

namespace PlaylistCreator;

use Exception;
use PlaylistCreator\Commands\CreateCommand;
use PlaylistCreator\Commands\ShowCommand;
use PlaylistCreator\Commands\StationsCommand;
use Symfony\Component\Console\Application;

require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

// hide deprecation notice in
// paquettg\php-html-parser\src\PHPHtmlParser\Dom\Node\Collection.php on line 133
error_reporting(E_ALL & ~E_DEPRECATED);

try {
    $app = new Application('Playlist Creator', 'v0.1');
    $app->add(new CreateCommand());
    $app->add(new ShowCommand());
    $app->add(new StationsCommand());
    $app->run();
} catch (Exception $e) {
    echo $e->getMessage();
}
