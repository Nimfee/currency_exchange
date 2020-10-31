<?php

namespace App\Service\Import;

use App\Service\Import\Builder\CurrencyExchangeRate\BuilderManager;
use App\Service\Import\Parser\CurrencyExchangeRate\ParserManager;

class Importer
{
    /**
     * @var ParserManager $parserManager
     */
    protected $parserManager;

    /**
     * @var BuilderManager $builderManager
     */
    protected $builderManager;

    /**
     * @param ParserManager $parserManager
     * @param BuilderManager $builderManager
     */
    public function __construct(ParserManager $parserManager, BuilderManager $builderManager)
    {
        $this->parserManager = $parserManager;
        $this->builderManager = $builderManager;
    }
    
    /**
     * @param $resource
     * @param string $currency
     * @param string $sourceType
     * @param string $clientType
     * @return void|null
     */
    public function processData($resource, string $isoCode, string $sourceType, string $builderType)
    {
        $data = $this->parserManager->getData($resource, $isoCode, $sourceType);

        if (count($data) === 0) {
            return null;
        }

        return $this->builderManager->createEntities($data, $builderType);
    }
}
