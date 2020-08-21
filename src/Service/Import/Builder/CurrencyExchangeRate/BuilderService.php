<?php

namespace App\Service\Import\Builder\CurrencyExchangeRate;

use App\Entity\Currency;
use App\Entity\CurrencyExchangeRate;
use App\Repository\CurrencyExchangeRateRepository;
use App\Repository\CurrencyRepository;
use Doctrine\ORM\EntityManagerInterface;

class BuilderService
{
    /** @var EntityManagerInterface  */
    protected $entityManager;

    /** @var CurrencyRepository  */
    protected $currencyRepository;

    /** @var CurrencyExchangeRateRepository  */
    protected $currencyExchangeRateRepository;

    /**
     * ParserManager constructor.
     * @param CurrencyRepository $currencyRepository
     * @param CurrencyExchangeRateRepository $currencyExchangeRateRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        CurrencyRepository $currencyRepository,
        CurrencyExchangeRateRepository $currencyExchangeRateRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->currencyRepository = $currencyRepository;
        $this->currencyExchangeRateRepository = $currencyExchangeRateRepository;
        $this->entityManager = $entityManager;
    }
    
    /**
     * @param array $items
     * @return int
     */
    public function createEntities(array $items)
    {
        $currencies = [];
        $count = 0;
        foreach ($items as $item) {
            $currencyFromISO = $item['currencyFrom'] ?? null;
            $currencyToISO = $item['currencyTo'] ?? null;
            if ((null === $currencyToISO) || (null === $currencyFromISO)) {
                continue;
            }

            if (array_key_exists($currencyToISO, $currencies)) {
                $currencyTo = $currencies[$currencyToISO];
            } else {
                $currencyTo = $this->getCurrencyFromIsoCode($currencyToISO);
                $currencies[$currencyTo->getISOCode()] = $currencyTo;
            }

            if (array_key_exists($currencyFromISO, $currencies)) {
                $currencyFrom = $currencies[$currencyFromISO];
            } else {
                $currencyFrom = $this->getCurrencyFromIsoCode($currencyFromISO);
                $currencies[$currencyFrom->getISOCode()] = $currencyFrom;
            }

            $currencyExchangeRate = $this->currencyExchangeRateRepository->findOneByCurrenciesAndRateDate(
                $currencyTo,
                $currencyFrom,
                $item['rateDate']
            );
            if (!$currencyExchangeRate) {
                $currencyExchangeRate = new CurrencyExchangeRate();
                $currencyExchangeRate->setCurrencyTo($currencyTo);
                $currencyExchangeRate->setCurrencyFrom($currencyFrom);
                $currencyExchangeRate->setRate($item['rate']);
                $currencyExchangeRate->setRateDate($item['rateDate']);
                $this->entityManager->persist($currencyExchangeRate);
                $count++;
            }
        }
        $this->entityManager->flush();
        $this->entityManager->clear();

        return $count;
    }

    /**
     * @param string $currencyISO
     * @return Currency
     */
    protected function getCurrencyFromIsoCode(string $currencyISO): Currency
    {
        $currency = $this->currencyRepository->getByIsoCode($currencyISO);

        if (null === $currency) {
            $currency = new Currency();
            $currency->setISOCode($currencyISO);
            $currency->setName($currencyISO);
            $this->entityManager->persist($currency);
            $this->entityManager->flush();
        }

        return $currency;
    }
}
