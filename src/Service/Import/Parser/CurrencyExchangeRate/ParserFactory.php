<?php

namespace App\Service\Import\Parser\CurrencyExchangeRate;

class ParserFactory
{
    const PARSER_ECB = 'ecb';
    const PARSER_BPI = 'bpi';

    /**
     * @var ParserBpi $parserBpi
     */
    protected $parserBpi;
 
    /**
     * @var ParserEcb $parserEcb
     */
    protected $parserEcb;

    public function __construct(iterable $parsers)
    {
        $handlers = iterator_to_array($parsers);
        $this->parserBpi = $handlers['bpi'];
        $this->parserEcb = $handlers['ecb'];
    }

    /**
     * @param string $sourceType
     * @return ParserBpi|ParserEcb|array
     */
    public function create(string $sourceType)
    {
        switch (strtolower($sourceType)) {
            case self::PARSER_ECB:
                return $this->parserEcb;
            case self::PARSER_BPI:
                return $this->parserBpi;
            default:
                throw new \InvalidArgumentException('Missing reader type');
        }
    }
}
