<?php

declare(strict_types=1);

namespace PlaylistCreator\Model;

final readonly class PlaylistIdentifier
{
    public function __construct(public ?string $id, public ?string $title)
    {
    }
}
