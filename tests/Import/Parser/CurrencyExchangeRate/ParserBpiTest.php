<?php

namespace App\Tests\Import\Parser\CurrencyExchangeRate;


use App\Service\Import\Parser\CurrencyExchangeRate\ParserBpi;
use Doctrine\Instantiator\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ParserBpiTest extends TestCase
{
    /**
     * @var ParserBpi
     */
    protected $instance;

    public function setUp()
    {
        $this->instance = new ParserBpi();
    }

    public function testParse()
    {
        $resource = file_get_contents(sprintf('%s/bpi.json', __DIR__));

        $actual = $this->instance->parse($resource, 'USD');

        self::assertCount(3, $actual);
    }
}
