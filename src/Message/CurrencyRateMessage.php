<?php

declare(strict_types=1);

namespace App\Message;

use DateTimeImmutable;

class CurrencyRateMessage
{
    public function __construct(private readonly DateTimeImmutable $onDate)
    {
    }

    public function getOnDate(): DateTimeImmutable
    {
        return $this->onDate;
    }
}
