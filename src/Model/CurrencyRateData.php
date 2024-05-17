<?php

declare(strict_types=1);

namespace App\Model;

class CurrencyRateData
{
    private array $data = [];

    public function __construct(
        private readonly CurrencyRateDates $currentRateDates,
        private readonly CurrencyRateDates $previousRateDates
    ) {
    }

    /**
     * @param CurrencyRateCollection $todayData
     * @param CurrencyRateCollection $previousData
     * @return CurrencyRateData
     */
    public static function fromRateCollections(
        CurrencyRateCollection $todayData,
        CurrencyRateCollection $previousData
    ): CurrencyRateData {
        $currencyRateData = new CurrencyRateData(
            $todayData->getCurrencyRateDates(),
            $previousData->getCurrencyRateDates()
        );

        /** @var CurrencyRate $data */
        foreach ($todayData->getRates() as $data) {
            $previousRate = $previousData->getCurrencyRate($data->getCurrency()->getSymbolCode());
            $currencyRateData->addRateData(
                new RateData(
                    $data->getCurrency(),
                    $data->getRate(),
                    $previousRate->getRate(),
                    $data->getNominal()
                )
            );
        }

        return $currencyRateData;
    }

    /**
     * @param RateData $rateData
     * @return self
     */
    public function addRateData(RateData $rateData): self
    {
        $this->data[$rateData->getCurrency()->getSymbolCode()] = $rateData;
        return $this;
    }

    /**
     * @param string $code
     * @return RateData|null
     */
    public function getRateData(string $code): ?RateData
    {
        return $this->data[$code] ?? null;
    }

    /**
     * @return CurrencyRateDates
     */
    public function getCurrentRateDates(): CurrencyRateDates
    {
        return $this->currentRateDates;
    }

    /**
     * @return CurrencyRateDates
     */
    public function getPreviousRateDates(): CurrencyRateDates
    {
        return $this->previousRateDates;
    }
}
