<?php

namespace App\Service\Import;

use App\Service\Import\Builder\CurrencyExchangeRate\BuilderCurrencyExchange;
use App\Service\Import\Parser\CurrencyExchangeRate\ParserManager;

class Importer
{
    /**
     * @var ParserManager $parserManager
     */
    protected $parserManager;

    /**
     * @var BuilderCurrencyExchange $builderService
     */
    protected $builderService;

    /**
     * @param ParserManager $parserManager
     * @param BuilderCurrencyExchange $builderService
     */
    public function __construct(ParserManager $parserManager, BuilderCurrencyExchange $builderService)
    {
        $this->parserManager = $parserManager;
        $this->builderService = $builderService;
    }
    
    /**
     * @param $resource
     * @param string $currency
     * @param string $sourceType
     * @return void|null
     */
    public function processData($resource, string $isoCode, string $sourceType)
    {
        $data = $this->parserManager->getData($resource, $isoCode, $sourceType);

        if (count($data) === 0) {
            return null;
        }

        return $this->builderService->createEntities($data);
    }
}
