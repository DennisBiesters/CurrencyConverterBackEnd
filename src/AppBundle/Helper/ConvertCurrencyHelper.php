<?php

namespace AppBundle\Helper;

use AppBundle\Classes\ConvertCurrency;
use AppBundle\Classes\ConvertCurrencyInterface;
use AppBundle\Classes\ResponseType;
use Unirest;

class ConvertCurrencyHelper implements ConvertCurrencyInterface
{
    public function Convert($currencyFrom, $currencyTo)
    {
        /**
         * Eerst in eigen database checken of gegevens die opgevraagd worden al bekend zijn, zo niet fallback naar webservices
         * in dit geval is er geen database, dus wordt dat overgeslagen
         */

        $webservices = array(
            new ConvertCurrency(
                "http://currencyconverter.kowabunga.net/converter.asmx/GetConversionRate",
                "CurrencyFrom",
                "CurrencyTo",
                ResponseType::XML
            ),
            new ConvertCurrency(
                "http://webservicex.net/CurrencyConvertor.asmx/ConversionRate",
                "FromCurrency",
                "ToCurrency",
                ResponseType::XML
            ),
            new ConvertCurrency(
                "https://api.fixer.io/latest",
                "base",
                "symbols",
                ResponseType::JSON
            ),
        );

        /**
         * Loop door webservices
         */
        foreach ($webservices as $webservice) {

            $parameters = array(
                $webservice->convertFromParam => $currencyFrom,
                $webservice->convertToParam => $currencyTo,
                'RateDate' => (new \DateTime())->format('m-d-Y')
            );

            /**
             * Geef elke webservice 5 seconde om te reageren
             */
            Unirest\Request::timeout(5);

            try {
                $response = Unirest\Request::get(
                    $webservice->url,
                    null,
                    $parameters
                );

                switch ($webservice->responseType) {
                    case ResponseType::XML:
                        /**
                         * Controleer op valide xml
                         */
                        libxml_use_internal_errors(true);
                        $xml = simplexml_load_string($response->body);
                        if (false === $xml) {
                            return 0;
                        }

                        return number_format(doubleval(simplexml_load_string($response->body)), 4);
                        break;
                    case ResponseType::JSON:

                        return number_format(doubleval($response->body->rates->$currencyTo), 4);
                        break;
                    default:
                        return 0;
                };

            } catch (\Exception $e) {
                /**
                 * Als webservice niet binnen 5 seconde reageert (en bij andere excepties) de loop door laten gaan
                 */
                continue;
            }

        }

        /**
         * Als geen enkele webservice heeft gereageerd dan 0 teruggeven
         */
        return 0;
    }
}