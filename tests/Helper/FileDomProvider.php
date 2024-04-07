<?php

declare(strict_types=1);

namespace tests\PlaylistCreator\Helper;

use Carbon\Carbon;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\ContentLengthException;
use PHPHtmlParser\Exceptions\LogicalException;
use PHPHtmlParser\Exceptions\StrictException;
use PHPHtmlParser\Options;
use PlaylistCreator\Exception\ExtractException;
use PlaylistCreator\Interface;

final readonly class FileDomProvider implements Interface\DomProvider
{
    public function __construct(private string $filename)
    {
    }

    /**
     * @throws ExtractException
     */
    public function fetchDOM(Carbon $datetime): Dom
    {
        try {
            $dom = new Dom();
            $dom->loadFromFile($this->filename, (new Options())->setWhitespaceTextNode(false));
        } catch (
            ChildNotFoundException|
            CircularException|
            ContentLengthException|
            LogicalException|
            StrictException $e
        ) {
            throw new ExtractException('Error loading dom: '.$this->filename, 0, $e);
        }

        return $dom;
    }
}
