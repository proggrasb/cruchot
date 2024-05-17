<?php

declare(strict_types=1);

namespace App\Model;

class CurrencyRate
{
    public function __construct(
        protected Currency $currency,
        protected readonly int $nominal,
        protected readonly string $rate,
        protected readonly string $unitRate,
    ) {
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getNominal(): int
    {
        return $this->nominal;
    }

    public function getRate(): string
    {
        return $this->rate;
    }

    public function getUnitRate(): string
    {
        return $this->unitRate;
    }
}
