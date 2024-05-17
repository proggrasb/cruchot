<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Currency;
use SimpleXMLElement;

class EnumCurrencyCbrWsdlParser
{
    public function parse(string $content): array
    {
        $result = [];

        $xml = new SimpleXMLElement($content);
        $currencies = $xml->xpath('//EnumValutes');
        foreach ($currencies as $currency) {
            $VnumCode = (string)$currency->VnumCode;
            $VcharCode = (string)$currency->VcharCode;
            if (!empty($VnumCode) && !empty($VcharCode)) {
                $Vname = (string)$currency->Vname;
                $Vname = trim(preg_replace('/\s+/', ' ', $Vname));

                $result[] = new Currency($VnumCode, $VcharCode, $Vname);
            }
        }

        return $result;
    }
}
