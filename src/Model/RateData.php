<?php

declare(strict_types=1);

namespace App\Model;

class RateData
{
    public function __construct(
        private readonly Currency $currency,
        private readonly string $currentRate,
        private readonly string $previousRate,
        private readonly int $nominal
    ) {
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getCurrentRate(): string
    {
        return $this->currentRate;
    }

    public function getPreviousRate(): string
    {
        return $this->previousRate;
    }

    public function getNominal(): int
    {
        return $this->nominal;
    }
}
