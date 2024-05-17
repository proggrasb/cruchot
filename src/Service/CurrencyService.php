<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Currency as CurrencyEntity;
use App\Entity\History;
use App\Entity\Rates;
use App\Exceptions\CurrencyError;
use App\Interfaces\DailyRateInterface;
use App\Interfaces\EnumCurrencyInterface;
use App\Model\CalculatedRate;
use App\Model\Currency;
use App\Model\CurrencyRate;
use App\Model\CurrencyRateData;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CurrencyService
{
    public function __construct(
        private readonly ParameterBagInterface $parameterBag,
        private readonly DailyRateInterface $dailyRate,
        private readonly EnumCurrencyInterface $enumCurrency,
        private readonly CurrencyRateCalculator $currencyRateCalculator,
        private readonly CacheInterface $cache,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return CurrencyRateData
     * @throws InvalidArgumentException
     */
    private function requestCurrencyRateData(): CurrencyRateData
    {
        return $this->cache->get('CBR_RATES', function (ItemInterface $item) {
            $item->expiresAfter($this->parameterBag->get('currency.cache.lifetime'));
            $current = $this->dailyRate->getOnDate(new DateTimeImmutable('now'));
            $previous = $this->dailyRate->getOnDate($current->getCurrencyRateDates()->getRateDate()->modify('-1 day'));

            return CurrencyRateData::fromRateCollections($current, $previous);
        });
    }

    public function prepareHistory(): void
    {
        $this->entityManager->getRepository(Rates::class)->deleteAll();
        $this->entityManager->getRepository(History::class)->deleteAll();
    }

    public function getAndStoreOnDate(DateTimeImmutable $date): ?DateTimeImmutable
    {
        $currencyRepository = $this->entityManager->getRepository(CurrencyEntity::class);

        $rates = $this->dailyRate->getOnDate($date);

        $rateEntity = (new Rates())
            ->setRatedAt(DateTime::createFromImmutable( $rates->getCurrencyRateDates()->getRateDate()));
        $this->entityManager->persist($rateEntity);

        /** @var CurrencyRate $rate */
        foreach ($rates->getRates() as $rate) {
            $currencyEntity = $currencyRepository->findOneBy(['scode' => $rate->getCurrency()->getSymbolCode()]);
            if ($currencyEntity instanceof CurrencyEntity) {
                $historyEntity = (new History())
                    ->setCurrencyId($currencyEntity->getId())
                    ->setRateId($rateEntity->getId())
                    ->setValueRate($rate->getRate())
                    ->setUnitRate($rate->getUnitRate())
                    ->setNominal($rate->getNominal());
                $this->entityManager->persist($historyEntity);
            }
        }

        $this->entityManager->flush();

        return $rates->getCurrencyRateDates()->getRateDate()->modify('-1 day');
    }

    /**
     * @param string $currency
     * @param string|null $baseCurrency
     * @return CalculatedRate
     * @throws InvalidArgumentException
     * @throws CurrencyError
     */
    public function getTodayRate(string $currency, ?string $baseCurrency = null): CalculatedRate
    {
        $defaultCurrency = $this->parameterBag->get('currency.default.symbolCode');
        if ($baseCurrency === $defaultCurrency) {
            $baseCurrency = null;
        }

        $currencyRateData = $this->requestCurrencyRateData();
        return is_null($baseCurrency)
            ? $this->currencyRateCalculator->calculateRate($currencyRateData, $currency)
            : $this->currencyRateCalculator->calculateCrossRate($currencyRateData, $currency, $baseCurrency);
    }

    public function getOnDateRate(string $currency, ?string $baseCurrency = null): ?CalculatedRate
    {
        $defaultCurrency = $this->parameterBag->get('currency.default.symbolCode');
        if ($baseCurrency === $defaultCurrency) {
            $baseCurrency = null;
        }
    }

    /**
     * @return void
     */
    public function prepareCurrencyTable(): void
    {
        if (0 === $this->entityManager->getRepository(CurrencyEntity::class)->count()) {
            /** @var Currency[] $currencies */
            $currencies = $this->enumCurrency->getEnumCurrencies();
            foreach ($currencies as $currency) {
                $newCurrency = (new CurrencyEntity())
                    ->setCode($currency->getCode())
                    ->setScode($currency->getSymbolCode())
                    ->setName($currency->getName());
                $this->entityManager->persist($newCurrency);
            }

            $this->entityManager->flush();
        }
    }
}
