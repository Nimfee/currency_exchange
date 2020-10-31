<?php

namespace App\Client\Service;

use Psr\Http\Message\ResponseInterface;

/**
 * Class File.
 */
class ClientManager
{
    /** @var ClientFactory  */
    protected $clientFactory;

    /** @var  string */
    protected $clientType;

    /**
     * ParserManager constructor.
     * @param ClientFactory $clientFactory
     */
    public function __construct(ClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }

    /**
     * Parses given resource and returns raw data.
     *
     * @param ResponseInterface $resource
     * @param string $path
     * @param string $sourceType
     *
     * @return array
     */
    public function executeRequest(ResponseInterface $resource, string $path, string $sourceType)
    {
        $client = $this->clientFactory->create($sourceType);

        return $client->executeRequest($resource->getBody(), $path);
    }
}
