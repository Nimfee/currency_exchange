<?php

namespace App\Service\Import\Parser\CurrencyExchangeRate;

use App\Service\Import\Parser\ParserInterface;

class ParserFactory
{
    const PARSER_ECB = 'text/xml';
    const PARSER_BPI = 'application/javascript';

    /**
     * @var ParserBpi $parserBpi
     */
    protected $parserBpi;
 
    /**
     * @var ParserEcb $parserEcb
     */
    protected $parserEcb;

    public function __construct(ParserBpi $parserBpi, ParserEcb $parserEcb)
    {
        $this->parserBpi = $parserBpi;
        $this->parserEcb = $parserEcb;
    }

    /**
     * @param string $type
     * @return ParserInterface
     */
    public function create($type)
    {
        switch (strtolower($type)) {
            case self::PARSER_ECB:
                return $this->parserEcb;
            case self::PARSER_BPI:
                return $this->parserBpi;
            default:
                throw new \InvalidArgumentException('Missing reader type');
        }
    }
}
