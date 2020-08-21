<?php

namespace App\Service\CurrencyExchange;

use App\Repository\CurrencyExchangeRateRepository;
use App\Repository\CurrencyRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Constraints\Date;

class CurrencyExchangeManager
{
    /** @var LoggerInterface  */
    protected $logger;
    
    /** @var CurrencyRepository  */
    protected $currencyExchangeRateRepository;

    /**
     * CurrencyExchangeManager constructor.
     * @param LoggerInterface $logger
     * @param CurrencyExchangeRateRepository $currencyExchangeRateRepository
     */
    public function __construct(LoggerInterface $logger, CurrencyExchangeRateRepository $currencyExchangeRateRepository)
    {
        $this->logger = $logger;
        $this->currencyExchangeRateRepository = $currencyExchangeRateRepository;
    }

    public function convert(int $from, int $to, $value): array
    {
        $result = $this->currencyExchangeRateRepository->findOneBy([
            'currencyFrom' => $from,
            'currencyTo' => $to,
            'rateDate' => new Date(),
        ]);
        if (null !== $result) {
            return $value * $result->getRate();
        }

        return $result;
    }

}