<?php

namespace App\Interfaces;

use DateTimeImmutable;

interface DailyRateInterface
{
    public function getOnDate(DateTimeImmutable $date);
}
