<?php
/**
 * Created by PhpStorm.
 * User: aleks
 * Date: 25.08.20
 * Time: 13:37
 */

namespace App\Tests\CurrencyExchange;


use App\Entity\CurrencyExchangeRate;
use App\Repository\CurrencyExchangeRateRepository;
use App\Service\CurrencyExchange\CurrencyExchangeManager;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;


class CurrencyExchangeManagerTest extends TestCase
{
    /**
     * @var \Psr\Log\LoggerInterface $logger
     */
    private $logger;

    /**
     * @var CurrencyExchangeRateRepository $currencyExchangeRateRepo
     */
    private $currencyExchangeRateRepo;

    public function setUp()
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->currencyExchangeRateRepo = $this->createMock(CurrencyExchangeRateRepository::class);
    }


    public function testProcessData()
    {
        $converter = new CurrencyExchangeManager($this->logger, $this->currencyExchangeRateRepo);

        $this->assertSame(118.5, $converter->processResult(2, 1, 100));

    }
}