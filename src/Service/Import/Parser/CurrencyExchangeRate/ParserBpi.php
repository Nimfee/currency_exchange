<?php

namespace App\Service\Import\Parser\CurrencyExchangeRate;

use App\Service\Import\Parser\ParserInterface;

class ParserBpi implements ParserInterface
{
    public function __construct()
    {
    }
    
    /**
     * @param $fileContent
     * @param string $currencyTo
     * @return array
     * @throws \Exception
     */
    public function parse($fileContent, string $currencyTo)
    {
        $rates = json_decode($fileContent, true);
        $rateCollection = [];
        $baseRateEntity = [
            'currencyTo' => $currencyTo,
            'currencyFrom' => 'BPI'
        ];
    
        if (isset($rates['bpi'])) {
            foreach ($rates['bpi'] as $date => $rate) {
                $rateEntity = $baseRateEntity;
                $rateEntity['rateDate'] = new \DateTime($date);
                $rateEntity['rate'] = $rate;
                $rateCollection[] = $rateEntity;
            }
        }

        return $rateCollection;
    }
}
