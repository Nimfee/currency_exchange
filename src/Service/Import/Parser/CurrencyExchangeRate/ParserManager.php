<?php

namespace App\Service\Import\Parser\CurrencyExchangeRate;
use App\Entity\Currency;
use Psr\Http\Message\ResponseInterface;

/**
 * Class File.
 */
class ParserManager
{
    /** @var ParserFactory  */
    protected $parserFactory;

    /**
     * ParserManager constructor.
     * @param ParserFactory $parserFactory
     */
    public function __construct(ParserFactory $parserFactory)
    {
        $this->parserFactory = $parserFactory;
    }

    /**
     * Parses given resource and returns raw data.
     *
     * @param ResponseInterface $resource
     * @param string $currencyTo
     * @param string $sourceType
     *
     * @return array
     */
    public function getData(ResponseInterface $resource, string $currencyTo, string $sourceType)
    {
        $parser = $this->parserFactory->create($sourceType);

        return $parser->parse($resource->getBody(), $currencyTo);
    }
}
