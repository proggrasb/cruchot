<?php

declare(strict_types=1);

namespace App\Service;

use App\Exceptions\CurrencyError;
use App\Model\Currency;
use App\Model\CurrencyRateData;
use App\Model\CalculatedRate;
use App\Model\RateData;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CurrencyRateCalculator
{
    public function __construct(private readonly ParameterBagInterface $parameterBag)
    {
    }

    /**
     * @param string $currentRate
     * @param string $previousRate
     * @return string
     */
    public static function calculateDiff(string $currentRate, string $previousRate): string
    {
        $diff = floatval($currentRate) - floatval($previousRate);
        return number_format($diff, 2, '.', '');
    }

    /**
     * @param CurrencyRateData $currencyRateData
     * @param string $currencySCode
     * @return CalculatedRate
     * @throws CurrencyError
     */
    public function calculateRate(CurrencyRateData $currencyRateData, string $currencySCode): CalculatedRate
    {
        $currencyRate = $currencyRateData->getRateData($currencySCode);

        if (!$currencyRate instanceof RateData) {
            throw new CurrencyError("Currency '$currencySCode' not found");
        }

        return new CalculatedRate(
            $currencyRate->getCurrency(),
            new Currency(
                $this->parameterBag->get('currency.default.code'),
                $this->parameterBag->get('currency.default.symbolCode'),
                $this->parameterBag->get('currency.default.name')
            ),
            $currencyRate->getNominal(),
            1,
            $currencyRate->getCurrentRate(),
            self::calculateDiff($currencyRate->getCurrentRate(), $currencyRate->getPreviousRate()),
            $currencyRateData->getCurrentRateDates()->getRateDate()->format('Y-m-d'),
            $currencyRateData->getPreviousRateDates()->getRateDate()->format('Y-m-d'),
            $currencyRateData->getCurrentRateDates()->getRequestRateDate()->format('Y-m-d H:i'),
        );
    }

    /**
     * @param CurrencyRateData $currencyRateData
     * @param string $currencySCode
     * @param string $baseCurrencySCode
     * @return CalculatedRate
     * @throws CurrencyError
     */
    public function calculateCrossRate(
        CurrencyRateData $currencyRateData,
        string $currencySCode,
        string $baseCurrencySCode
    ): CalculatedRate {
        $currencyRate = $currencyRateData->getRateData($currencySCode);
        if (!$currencyRate instanceof RateData) {
            throw new CurrencyError("Currency '$currencySCode' not found");
        }

        $baseCurrencyRate = $currencyRateData->getRateData($baseCurrencySCode);
        if (!$baseCurrencyRate instanceof RateData) {
            throw new CurrencyError("Currency '$baseCurrencySCode' not found");
        }

        $currencyCurrentRateValue = floatval($currencyRate->getCurrentRate()) / $currencyRate->getNominal();
        $baseCurrentCurrencyRateValue = floatval($baseCurrencyRate->getCurrentRate()) / $baseCurrencyRate->getNominal();
        $crossCurrentRateValue = number_format(
            ($currencyCurrentRateValue / $baseCurrentCurrencyRateValue),
            6,
            '.',
            ''
        );
        $currencyPreviousRateValue = floatval($currencyRate->getPreviousRate()) / $currencyRate->getNominal();
        $basePreviousCurrencyRateValue = floatval($baseCurrencyRate->getPreviousRate()) / $baseCurrencyRate->getNominal(
            );
        $crossPreviousRateValue = number_format(
            $currencyPreviousRateValue / $basePreviousCurrencyRateValue,
            6,
            '.',
            ''
        );

        return new CalculatedRate(
            $currencyRate->getCurrency(),
            $baseCurrencyRate->getCurrency(),
            1,
            1,
            $crossCurrentRateValue,
            self::calculateDiff($crossCurrentRateValue, $crossPreviousRateValue),
            $currencyRateData->getCurrentRateDates()->getRateDate()->format('Y-m-d'),
            $currencyRateData->getPreviousRateDates()->getRateDate()->format('Y-m-d'),
            $currencyRateData->getCurrentRateDates()->getRequestRateDate()->format('Y-m-d H:i'),
        );
    }
}
