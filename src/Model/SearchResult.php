<?php

declare(strict_types=1);

namespace PlaylistCreator\Model;

final readonly class SearchResult
{
    public function __construct(public string $title, public string $id)
    {
    }
}
