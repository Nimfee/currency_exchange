<?php

namespace App\Tests\Import\Parser\CurrencyExchangeRate;


use App\Service\Import\Parser\CurrencyExchangeRate\ParserEcb;
use Doctrine\Instantiator\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ParserEcbTest extends TestCase
{
    /**
     * @var ParserEcb
     */
    protected $instance;

    public function setUp()
    {
        $this->instance = new ParserEcb();
    }

    public function testParse()
    {
        $resource = file_get_contents(sprintf('%s/ecb.xml', __DIR__));

        $actual = $this->instance->parse($resource, 'EUR');

        self::assertCount(3, $actual);
    }
}
