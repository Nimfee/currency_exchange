<?php

namespace App\Service\Import\Parser\CurrencyExchangeRate;

use App\Service\Import\Parser\ParserInterface;
use Psr\Log\LoggerInterface;

class ParserEcb implements ParserInterface
{
    /** @var LoggerInterface  */
    protected $logger;

    /**
     * ReaderXml constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
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
