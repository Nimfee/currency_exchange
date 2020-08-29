<?php

namespace App\Service\CurrencyExchange;

use App\Entity\CurrencyExchangeRate;
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


    /**
     * @param int $from
     * @param int $to
     * @param float $value
     * @return float
     */
    public function processResult(int $from, int $to, float $value): float
    {
        $exchangeRate = null;
        $exchangeRate = $this->currencyExchangeRateRepository->findCurrencyExchangeRate($from, $to);
        if (null !== $exchangeRate) {
            return $this->getValueByRate($from, $exchangeRate, $value);
        }
        $commonCurrency = $this->currencyExchangeRateRepository->findComplexCurrencyExchangeRate($from, $to);

        if (count($commonCurrency) > 0) {
            $ids = array_shift($commonCurrency);
            if (is_array($ids)) {
                /** @var CurrencyExchangeRate $exchangeRate */
                $exchangeRateFrom = $this->currencyExchangeRateRepository->findCurrencyExchangeRate(
                    $ids['from'], $ids['to']
                );

                $value = $this->getValueByRate($ids['from'], $exchangeRateFrom, $value);
            } else {
                $ids = ['from' => $ids, 'to' => $ids];
            }
            /** @var CurrencyExchangeRate $exchangeRate */
            $exchangeRateFrom = $this->currencyExchangeRateRepository->findCurrencyExchangeRate($from, $ids['from']);

            $value = null !== $exchangeRateFrom ? $this->getValueByRate($from, $exchangeRateFrom, $value) : 0;

            $exchangeRateTo = $this->currencyExchangeRateRepository->findCurrencyExchangeRate($to,  $ids['to']);

            return  null !== $exchangeRateTo ? $this->getValueByRate($ids['to'], $exchangeRateTo, $value) : 0;
        }

        return 0;
    }

    /**
     * @param int $currency
     * @param CurrencyExchangeRate $currencyExchangeRate
     * @param float $value
     * @return float
     */
    protected function getValueByRate(
        int $currency,
        CurrencyExchangeRate $currencyExchangeRate,
        float $value
    )
    {
        return $currencyExchangeRate->getCurrencyFrom()->getId() == $currency ?
            $value * $currencyExchangeRate->getRate() : $value / $currencyExchangeRate->getRate();
    }
}