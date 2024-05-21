<?php

declare(strict_types=1);

namespace App\Command;

use App\Message\CurrencyRateMessage;
use App\Service\CurrencyService;
use DateTimeImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Service\Attribute\Required;

#[AsCommand(name: 'app:get-currency-rates', description: 'Get all currency rates for last 180 days')]
class GetCurrencyRatesCommand extends Command
{
    private MessageBusInterface $messageBus;
    private CurrencyService $currencyService;

    #[Required]
    public function setMessageBus(MessageBusInterface $messageBus): void
    {
        $this->messageBus = $messageBus;
    }

    #[Required]
    public function setCurrencyService(CurrencyService $currencyService): void
    {
        $this->currencyService = $currencyService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("Checking currency list...");
        $this->currencyService->prepareCurrencyTable();

        $output->writeln("Clearing history...");
        $this->currencyService->prepareHistory();

        $output->writeln("Fetching rates of currencies. It make takes several minutes.");
        $this->messageBus->dispatch(new CurrencyRateMessage(new DateTimeImmutable('now')));

        return Command::SUCCESS;
    }
}
