<?php

namespace AppBundle\Controller;

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
     * @Route("/getAllWebservices")
     */
    public function getAllWebservices()
    {
        return new JsonResponse(
            array(
                'webservicex' => 'http://currencyconverter.kowabunga.net/converter.asmx/GetConversionRate',
                'currencyconverter' => 'http://currencyconverter.kowabunga.net/converter.asmx',
                'fixerio' => 'https://api.fixer.io/latest',
            )
        );
    }

    /**
     * @Route("/ConvertCurrency")
     */
    public function convertCurrency(Request $request)
    {
        $webservice = $request->get("ws_name");

        if (!empty($webservice)) {
            switch ($webservice) {
                case "webservicex";
                    $parameters = array('FromCurrency' => 'USD', 'ToCurrency' => 'EUR');
                    $response = Unirest\Request::get(
                        'http://webservicex.net/CurrencyConvertor.asmx/ConversionRate',
                        null,
                        $parameters
                    );
                    $response = simplexml_load_string($response->body);
                    break;
                case "currencyconverter";
                    $parameters = array('CurrencyFrom' => 'USD', 'CurrencyTo' => 'EUR', 'RateDate' => '1-26-2018');
                    $response = Unirest\Request::get(
                        'http://currencyconverter.kowabunga.net/converter.asmx/GetConversionRate',
                        null,
                        $parameters
                    );
                    $response = simplexml_load_string($response->body);
                    break;
                case "fixerio";
                    //$headers = array('Accept' => 'application/json');
                    $parameters = array('base' => 'USD', 'symbols' => 'EUR');
                    $response = Unirest\Request::get('https://api.fixer.io/latest', null, $parameters)->body;
                    break;
                default;
                    throw new BadRequestHttpException();
            };
        } else {
            throw new BadRequestHttpException();
        }

        return new JsonResponse(
            array(
                'response' => $response,
            )
        );
    }
}
