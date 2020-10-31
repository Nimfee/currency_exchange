<?php

namespace App\Service\Import\Builder\CurrencyExchangeRate;


/**
 * Class File.
 */
class BuilderManager
{
    /** @var BuilderFactory  */
    protected $parserFactory;

    /**
     * ParserManager constructor.
     * @param BuilderFactory $builderFactory
     */
    public function __construct(BuilderFactory $builderFactory)
    {
        $this->builderFactory = $builderFactory;
    }

    /**
     * Parses given resource and returns raw data.
     *
     * @param array $data
     * @param string $builderType
     *
     * @return array
     */
    public function createEntities(array $data, string $builderType)
    {
        $builder = $this->builderFactory->create($builderType);

        return $builder->createEntities($data);
    }
}
