<?php

declare(strict_types=1);

namespace App\Service;

use App\Interfaces\DailyRateInterface;
use App\Interfaces\EnumCurrencyInterface;
use App\Model\CurrencyRateCollection;
use DateTimeImmutable;
use Exception;
use SoapFault;

class CurrencyDailyCbrWsdlService implements DailyRateInterface, EnumCurrencyInterface
{
    public function __construct(
        private readonly CbrDailySoapClientFactory $soapClientFactory,
        private readonly CurrencyDailyCbrWsdlBuilder $builder,
        private readonly EnumCurrencyCbrWsdlParser $enumCurrencyParser
    ) {
    }

    /**
     * @param DateTimeImmutable $date
     * @return CurrencyRateCollection|null
     * @throws SoapFault
     * @throws Exception
     */
    public function getOnDate(DateTimeImmutable $date): ?CurrencyRateCollection
    {
        $soapClient = $this->soapClientFactory->make();
        $rates = $soapClient->GetCursOnDate(['On_date' => $date->format('Y-m-d')]);
        return $this->builder->build($rates->GetCursOnDateResult->schema, $rates->GetCursOnDateResult->any);
    }

    /**
     * @return array
     * @throws SoapFault
     */
    public function getEnumCurrencies(): array
    {
        $soapClient = $this->soapClientFactory->make();
        $enums = $soapClient->EnumValutes(['Seld' => false]);
        return $this->enumCurrencyParser->parse($enums->EnumValutesResult->any);
    }
}
