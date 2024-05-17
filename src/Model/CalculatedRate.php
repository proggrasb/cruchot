<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class CalculatedRate implements JsonSerializable
{
    public function __construct(
        private readonly Currency $currency,
        private readonly Currency $baseCurrency,
        protected readonly int $currencyNominal,
        protected readonly int $baseCurrencyNominal,
        protected readonly string $rate,
        protected readonly string $change,
        protected readonly string $onDate,
        protected readonly string $previousDate,
        protected readonly string $requestTime,
    ) {
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getBaseCurrency(): Currency
    {
        return $this->baseCurrency;
    }

    public function getCurrencyNominal(): int
    {
        return $this->currencyNominal;
    }

    public function getRate(): string
    {
        return $this->rate;
    }

    public function getChange(): string
    {
        return $this->change;
    }

    public function getOnDate(): string
    {
        return $this->onDate;
    }

    public function getPreviousDate(): string
    {
        return $this->previousDate;
    }

    public function getRequestTime(): string
    {
        return $this->requestTime;
    }

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
