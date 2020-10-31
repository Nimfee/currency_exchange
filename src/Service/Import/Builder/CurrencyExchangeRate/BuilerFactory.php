<?php

namespace App\Service\Import\Builder\CurrencyExchangeRate;

class BuilderFactory
{
    const BUILDER_CE = 'currency_exchange';

    /**
     * @var BuilderCurrencyExchange $builderCE
     */
    protected $builderCE;

    public function __construct(iterable $builders)
    {
        $handlers = iterator_to_array($builders);
        $this->builderCE = $handlers['currency_exchange'];
    }

    /**
     * @param string $sourceType
     * @return $builderCE|array
     */
    public function create(string $sourceType)
    {
        switch (strtolower($sourceType)) {
            case self::BUILDER_CE:
                return $this->builderCE;
            default:
                throw new \InvalidArgumentException('Missing reader type');
        }
    }
}
