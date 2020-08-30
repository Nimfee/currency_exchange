<?php

namespace App\Service\Import\Parser\CurrencyExchangeRate;

use App\Service\Import\Parser\ParserInterface;

class ParserEcb implements ParserInterface
{
    /**
     * ReaderXml constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param $fileContent
     * @param string $currencyFrom
     */
    public function parse($fileContent, string $currencyFrom)
    {
        $rates = new \SimpleXMLElement($fileContent);
        $rateCollection = [];
        $baseRateEntity = [
            'rateDate' => new \DateTime((string)$rates->Cube[0]->Cube[0]->attributes()),
            'currencyFrom' => $currencyFrom
        ];

        foreach ($rates->Cube[0]->Cube->Cube as $cube) {
            $attributes = $cube->attributes();
            $rateEntity = $baseRateEntity;
            if (isset($attributes['currency'])) {
                $rateEntity['currencyTo'] = (string) $attributes['currency'];
            }
            if (isset($attributes['rate'])) {
                $rateEntity['rate'] = (float) $attributes['rate'];
            }
            $rateCollection[] = $rateEntity;
        }

        return $rateCollection;
    }
}
