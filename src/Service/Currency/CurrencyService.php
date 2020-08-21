<?php
namespace App\Service\Currency;

use App\Repository\CurrencyRepository;
use Psr\Log\LoggerInterface;

class CurrencyService
{
    /** @var LoggerInterface  */
    protected $logger;

    /** @var CurrencyRepository  */
    protected $currencyRepository;

    /**
     * CurrencyService constructor.
     * @param LoggerInterface $logger
     * @param CurrencyRepository $currencyRepository
     */
    public function __construct(LoggerInterface $logger, CurrencyRepository $currencyRepository)
    {
        $this->logger = $logger;
        $this->currencyRepository = $currencyRepository;
    }
    public function getFormattedCurrencies()
    {
        $currencies = $this->currencyRepository->getAllInArray();
    
        return is_array($currencies) ?
            array_column($currencies, 'id', 'ISO_code')
            : [];
    }
}