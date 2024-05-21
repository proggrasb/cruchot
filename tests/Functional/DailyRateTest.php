<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Exceptions\CurrencyError;
use App\Service\CbrDailySoapClientFactory;
use App\Service\CurrencyService;
use App\Tests\Functional\Data\DailyData;
use DateTime;
use Exception;
use loophp\MockSoapClient\MockSoapClient;
use Psr\Cache\InvalidArgumentException;
use SoapFault;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DailyRateTest extends KernelTestCase
{
    /**
     * @return void
     *
     * @throws CurrencyError
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function testDailyRate(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $mock = $this->getMockBuilder(CbrDailySoapClientFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock->method('make')->willReturn(
            new MockSoapClient(
                static function ($method, $arguments) {
                    if ($method == 'GetCursOnDate') {
                        if ($arguments[0]['On_date'] === (new DateTime())->format('Y-m-d')) {
                            return DailyData::today();
                        }
                        return DailyData::previous();
                    }

                    throw new SoapFault('Server', sprintf('Unknown SOAP method "%s"', $method));
                }
            )
        );

        $container->set(CbrDailySoapClientFactory::class, $mock);

        // testing
        /** @var CurrencyService $currencyService */
        $currencyService = $container->get(CurrencyService::class);
        $currencyRate = $currencyService->getTodayRate('USD');

        self::assertEquals("90.6537", $currencyRate->getRate());
        self::assertEquals(1, $currencyRate->getCurrencyNominal());
        self::assertEquals('USD', $currencyRate->getCurrency()->getSymbolCode());
        self::assertEquals('RUR', $currencyRate->getBaseCurrency()->getSymbolCode());
        self::assertEquals("-0.33", $currencyRate->getChange());
    }
}
