<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\CurrencyRateMessage;
use App\Service\CurrencyService;
use DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
class CurrencyRateHandler
{
    public function __construct(
        private readonly ParameterBagInterface $parameterBag,
        private readonly MessageBusInterface $messageBus,
        private readonly CurrencyService $currencyService,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(CurrencyRateMessage $currencyRate): void
    {
        $this->logger->info("Handle currency rates on date " . $currencyRate->getOnDate()->format('Y-m-d'));
        $days = (new DateTime("now"))->diff($currencyRate->getOnDate())->days;
        if ($days <= (int)$this->parameterBag->get('worker.count.days')) {
            if ($nextDate = $this->currencyService->getAndStoreOnDate($currencyRate->getOnDate())) {
                $this->logger->info("Send new message for date " . $nextDate->format('Y-m-d'));
                $this->messageBus->dispatch(new CurrencyRateMessage($nextDate));
                return;
            }
        }

        $this->logger->info("Handler of currency rates finished");
    }
}
