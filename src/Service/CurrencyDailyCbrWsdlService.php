<?php

declare(strict_types=1);

namespace App\Service;

use App\Interfaces\DailyRateInterface;
use App\Interfaces\EnumCurrencyInterface;
use App\Model\CurrencyRateCollection;
use DateTimeImmutable;
use SoapClient;
use SoapFault;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CurrencyDailyCbrWsdlService implements DailyRateInterface, EnumCurrencyInterface
{
    public function __construct(
        private readonly ParameterBagInterface $parameterBag,
        private readonly CurrencyDailyCbrWsdlBuilder $builder,
        private readonly EnumCurrencyCbrWsdlParser $enumCurrencyParser
    ) {
    }

    /**
     * @return SoapClient
     * @throws SoapFault
     */
    private function getSoapClient(): SoapClient
    {
        $options = [
            'soap_version' => SOAP_1_1,
            'exceptions' => true,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'use' => SOAP_LITERAL,
            'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
            'encoding' => 'UTF-8',
        ];
        $url = $this->parameterBag->get('cbr_asmx_daily_web_srv.url') . '?wsdl';
        return new SoapClient($url, $options);
    }

    /**
     * @param DateTimeImmutable $date
     * @return CurrencyRateCollection|null
     * @throws SoapFault
     */
    public function getOnDate(DateTimeImmutable $date): ?CurrencyRateCollection
    {
        $soapClient = $this->getSoapClient();
        $rates = $soapClient->GetCursOnDate(['On_date' => $date->format('Y-m-d')]);
        return $this->builder->build($rates->GetCursOnDateResult->schema, $rates->GetCursOnDateResult->any);
    }

    /**
     * @return array
     * @throws SoapFault
     */
    public function getEnumCurrencies(): array
    {
        $soapClient = $this->getSoapClient();
        $enums = $soapClient->EnumValutes(['Seld' => false]);
        return $this->enumCurrencyParser->parse($enums->EnumValutesResult->any);
    }
}
