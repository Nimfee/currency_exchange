<?php

namespace App\Client\Service;


class ClientFactory
{
    const CLIENT_CURL = 'curl';

    /**
     * @var CurlService $clientCurl
     */
    protected $clientCurl;

    public function __construct(iterable $clients)
    {
        $handlers = iterator_to_array($clients);
        $this->clientCurl = $handlers['curl'];
    }

    /**
     * @param string $sourceType
     * @return CurlService|array
     */
    public function create(string $sourceType)
    {
        switch (strtolower($sourceType)) {
            case self::CLIENT_CURL:
                return $this->clientCurl;
            default:
                throw new \InvalidArgumentException('Missing client type');
        }
    }
}
