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

    /** @var  string */
    protected $readerType;

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
     *
     * @return array
     */
    public function getData($resource, $currencyTo)
    {
        $type = $resource->getHeaderLine('content-type');
        $parser = $this->parserFactory->create($type);

        return $parser->parse($resource->getBody(), $currencyTo);
    }
}
