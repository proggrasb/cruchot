<?php

namespace App\Service;

use App\Exceptions\SoapParserError;
use App\Model\Currency;
use App\Model\CurrencyRate;
use App\Model\CurrencyRateCollection;
use App\Model\CurrencyRateDates;
use DateTimeImmutable;
use Exception;
use SimpleXMLElement;

class CurrencyDailyCbrWsdlBuilder
{
    private ?CurrencyRateCollection $currencyRateCollection= null;

    /**
     * @param string $content
     * @return DateTimeImmutable|null
     * @throws Exception
     */
    private function parseOnDate(string $content): ?DateTimeImmutable
    {
        $xml = new SimpleXMLElement($content);
        $onDateResult = $xml->xpath('xs:element[@name="ValuteData"]/@msprop:OnDate');

        if ($onDateResult !== false && count($onDateResult) > 0) {
            $onDate = (string)$onDateResult[0]->OnDate[0];
            return DateTimeImmutable::createFromFormat('Ymd', $onDate)->setTime(0, 0);
        }

        throw new SoapParserError("Cannot find attribute 'OnDate'");
    }

    /**
     * @param string $content
     * @return void
     * @throws Exception
     */
    private function parseCurrenciesRates(string $content): void
    {
        $xml = new SimpleXMLElement($content);
        $rates = $xml->xpath('//ValuteCursOnDate');
        foreach ($rates as $rate) {
            $Vcode  = (string)$rate->Vcode;
            $VchCode = (string)$rate->VchCode;
            $Vname = (string)$rate->Vname;
            $Vname = trim(preg_replace('/\s+/', ' ', $Vname));
            $Vnom = (int)$rate->Vnom;
            $Vcurs = (string)$rate->Vcurs;
            $VunitRate = (string)$rate->VunitRate;
            $this->currencyRateCollection->addCurrencyRate(
                new CurrencyRate(
                    new Currency($Vcode, $VchCode, $Vname),
                    $Vnom,
                    $Vcurs,
                    $VunitRate
                )
            );
        }
    }

    /**
     * @param string $schema
     * @param string $rates
     * @return CurrencyRateCollection
     * @throws Exception
     */
    public function build(string $schema, string $rates): CurrencyRateCollection
    {
        $rateDate = $this->parseOnDate($schema);
        $this->currencyRateCollection = new CurrencyRateCollection(
            new CurrencyRateDates(
                (new DateTimeImmutable('now')),
                $rateDate
            )
        );
        $this->parseCurrenciesRates($rates);
        $result = $this->currencyRateCollection;
        $this->currencyRateCollection = null;

        return $result;
    }
}
