<?php

declare(strict_types=1);

namespace PlaylistCreator\Model;

use Carbon\Carbon;
use JetBrains\PhpStorm\Pure;
use PlaylistCreator\Interface\LimitFilterable;

final readonly class Song implements \Stringable, LimitFilterable
{
    public function __construct(
        private string $artist,
        private string $title,
        private Carbon $timestamp
    ) {
    }

    #[Pure]
    public function __toString(): string
    {
        return "\"{$this->artist}\", \"{$this->title}\"";
    }

    public function getTimestamp(): Carbon
    {
        return $this->timestamp;
    }
}
