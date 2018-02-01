<?php

namespace AppBundle\Controller;

use AppBundle\Helper\ConvertCurrencyHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @Route("/api")
 */
class ApiController extends Controller
{
    /**
     * @Route("/GetAllCurrencies")
     */
    public function getAllCurrencies()
    {
        $convertHelper = new ConvertCurrencyHelper();

        return new JsonResponse(
            array(
                'currencies' => $convertHelper->getAllCurrencies(),
            )
        );
    }

    /**
     * @Route("/ConvertCurrency")
     */
    public function convertCurrency(Request $request)
    {
        $amount = $request->get("amount");
        $currencyFrom = $request->get("currency_from");
        $currencyTo = $request->get("currency_to");

        $convertHelper = new ConvertCurrencyHelper();
        $currencies = $convertHelper->getAllLocalCurrencies();

        /**
         * Controleer input en of from en to currency correct is
         */
        if (!empty($amount) && is_numeric($amount) && $amount > 0 && !(empty($currencyFrom)) && !(empty($currencyTo)) && in_array($currencyFrom, $currencies) && in_array($currencyTo, $currencies)) {

            $convertHelper = new ConvertCurrencyHelper();

        } else {
            throw new BadRequestHttpException();
        }

        return new JsonResponse(
            array(
                'rate' => $convertHelper->Convert($currencyFrom, $currencyTo) * $amount,
            )
        );
    }
}
