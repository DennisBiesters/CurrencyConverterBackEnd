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
     * @Route("/ConvertCurrency")
     */
    public function convertCurrency(Request $request)
    {
        $webservice = $request->get("ws_name");
        $currencyFrom = $request->get("currency_from");
        $currencyTo = $request->get("currency_to");

        if (!empty($webservice) && !(empty($currencyFrom)) && !(empty($currencyTo))) {
            switch ($webservice) {
                case "webservicex";
                    $parameters = array('FromCurrency' => $currencyFrom, 'ToCurrency' => $currencyTo);
                    $response = Unirest\Request::get(
                        'http://webservicex.net/CurrencyConvertor.asmx/ConversionRate',
                        null,
                        $parameters
                    );
                    $response = number_format(doubleval(simplexml_load_string($response->body)), 4);
                    break;
                case "currencyconverter";
                    $parameters = array('CurrencyFrom' => $currencyFrom, 'CurrencyTo' => $currencyTo, 'RateDate' => '1-26-2018');
                    $response = Unirest\Request::get(
                        'http://currencyconverter.kowabunga.net/converter.asmx/GetConversionRate',
                        null,
                        $parameters
                    );
                    //var_dump((string) simplexml_load_string($response->body));
                    //var_dump($response->body);
                    //var_dump(number_format(doubleval("a")));
                    //var_dump(number_format(doubleval(simplexml_load_string("a"))));
                    //var_dump(simplexml_load_string("a"));
                    $response = number_format(doubleval(simplexml_load_string($response->body)), 4);
                    break;
                case "fixerio";
                    //$headers = array('Accept' => 'application/json');
                    $parameters = array('base' => $currencyFrom, 'symbols' => $currencyTo);
                    $response = number_format(
                        doubleval(
                            Unirest\Request::get('https://api.fixer.io/latest', null, $parameters)->body->rates->$currencyTo
                        ),
                        4
                    );
                    break;
                default;
                    throw new BadRequestHttpException();
            };
        } else {
            throw new BadRequestHttpException();
        }

        return new JsonResponse(
            array(
                'rate' => $response,
            )
        );
    }
}
