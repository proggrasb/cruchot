<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exceptions\CurrencyError;
use App\Service\CurrencyService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CurrencyController extends AbstractController
{

    #[Route('/currency/current', name: 'currency-current', methods: ['GET'])]
    public function index(Request $request, CurrencyService $currencyService, LoggerInterface $logger): Response
    {
        $currency = $request->get("currency");
        $baseCurrency = $request->get("baseCurrency") ?? null;

        if ($currency === $baseCurrency) {
            $logger->error("Currency cannot be the same as BaseCurrency");
            return $this->json(['error' => 'Currency cannot be the same as BaseCurrency'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $rateInfo = $currencyService->getTodayRate($currency, $baseCurrency);
        } catch(CurrencyError $e) {
            $logger->error($e->getMessage());
            return $this->json(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch(\Throwable $e) {
            $logger->error($e->getMessage());
            return $this->json(['error' => 'Something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($rateInfo);
    }
}
