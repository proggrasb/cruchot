<?php

declare(strict_types=1);

namespace App\Model;

class CurrencyRateCollection
{
    private array $rates = [];

    public function __construct(
        private readonly CurrencyRateDates $currencyRateDates
    ) {
    }

    public function getCurrencyRateDates(): CurrencyRateDates
    {
        return $this->currencyRateDates;
    }

    public function addCurrencyRate(CurrencyRate $currencyRate): void
    {
        $this->rates[$currencyRate->getCurrency()->getSymbolCode()] = $currencyRate;
    }

    public function getCurrencyRate(string $code): ?CurrencyRate
    {
        return $this->rates[$code] ?? null;
    }

    public function getRates(): array
    {
        return $this->rates;
    }
}
