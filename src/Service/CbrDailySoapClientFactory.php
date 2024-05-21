<?php

namespace App\Service;

use SoapClient;
use SoapFault;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CbrDailySoapClientFactory
{
    public function __construct(
        private readonly ParameterBagInterface $parameterBag
    ) {
    }

    /**
     * @return SoapClient
     * @throws SoapFault
     */
    public function make(): SoapClient
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
}
