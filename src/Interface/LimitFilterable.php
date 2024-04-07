<?php

declare(strict_types=1);

namespace PlaylistCreator\Interface;

use Carbon\Carbon;

interface LimitFilterable
{
    public function getTimestamp(): Carbon;
}
