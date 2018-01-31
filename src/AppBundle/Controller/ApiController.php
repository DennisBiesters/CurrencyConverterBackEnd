<?php

namespace AppBundle\Controller;

use AppBundle\Helper\ConvertCurrencyHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Unirest;

/**
 * @Route("/api")
 */
class ApiController extends Controller
{
    /**
     * @Route("/GetAllWebservices")
     */
    public function getAllWebservices()
    {
        return new JsonResponse(
            array(
                'webservices' => array(
                    'webservicex' => 'http://currencyconverter.kowabunga.net/converter.asmx/GetConversionRate',
                    'currencyconverter' => 'http://currencyconverter.kowabunga.net/converter.asmx',
                    'fixerio' => 'https://api.fixer.io/latest',
                ),
            )
        );
    }

    /**
     * @Route("/GetAllCurrencies")
     */
    public function getAllCurrencies()
    {
        $response = Unirest\Request::get(
            'https://api.fixer.io/latest',
            null,
            null
        );

        $currencies = array();
        $currencies[] = "EUR";

        foreach($response->body->rates as $key => $value){
            $currencies[] = $key;
        }

        return new JsonResponse(
            array(
                'currencies' => $currencies,
            )
        );
    }

    /**
     * @Route("/ConvertCurrency")
     */
    public function convertCurrency(Request $request)
    {
        $currencyFrom = $request->get("currency_from");
        $currencyTo = $request->get("currency_to");

        if (!(empty($currencyFrom)) && !(empty($currencyTo))) {

            $convertHelper = new ConvertCurrencyHelper();

        } else {
            throw new BadRequestHttpException();
        }

        return new JsonResponse(
            array(
                'rate' => $convertHelper->Convert($currencyFrom, $currencyTo),
            )
        );
    }
}
