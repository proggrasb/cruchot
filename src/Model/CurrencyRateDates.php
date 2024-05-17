<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;

class CurrencyRateDates
{
    public function __construct(
        private readonly DateTimeImmutable $requestRateDate,
        private readonly DateTimeImmutable $rateDate
    ) {
    }

    public function getRequestRateDate(): DateTimeImmutable
    {
        return $this->requestRateDate;
    }

    public function getRateDate(): DateTimeImmutable
    {
        return $this->rateDate;
    }
}
