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
                "http://webservicex.net/CurrencyConvertor.asmx/ConversionRate",
                "FromCurrency",
                "ToCurrency",
                ResponseType::XML,
                false
            ),
            new ConvertCurrency(
                "http://currencyconverter.kowabunga.net/converter.asmx/GetConversionRate",
                "CurrencyFrom",
                "CurrencyTo",
                ResponseType::XML,
                true
            ),
            new ConvertCurrency(
                "https://api.fixer.io/latest",
                "base",
                "symbols",
                ResponseType::JSON,
                true
            ),
        );

        /**
         * Loop door webservices
         */
        foreach ($webservices as $webservice) {

            $parameters = array(
                $webservice->convertFromParam => $currencyFrom,
                $webservice->convertToParam => $currencyTo,
                'RateDate' => (new \DateTime())->format('m-d-Y'),
            );

            /**
             * Eigenlijk moet er om de zoveel tijd gekeken worden of een webservice nog up is en een correct antwoord geeft
             * de webservicex api geeft altijd -1 als antwoord, dus daarom wordt deze overgeslagen
             */
            if(!$webservice->available){
                continue;
            }

            $output = 0;
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

                        $output = doubleval(simplexml_load_string($response->body));
                        break;
                    case ResponseType::JSON:

                        $output = doubleval($response->body->rates->$currencyTo);
                        break;
                    default:
                        return 0;
                };

                /**
                 * Als webservice een negatieve waarde teruggeeft de loop door laten gaan zodat een andere webservice een correct antwoord kan teruggeven
                 */
                if ($output <= 0) {
                    continue;
                }

                /**
                 * Zet precision om 4 punten achter de komma
                 */
                return number_format($output, 4);

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

    public function getAllCurrencies()
    {
        $currencies = array();

        /**
         * Geef  webservice 5 seconde om te reageren
         */
        Unirest\Request::timeout(5);

        $response = '';

        try {
            $response = Unirest\Request::get(
                'https://api.fixer.io/latest',
                null,
                null
            );
        } catch (\Exception $e) {
            /**
             * Fallback als webservice niet (op tijd) reageert
             */
            $currencies = $this->getAllLocalCurrencies();
        }

        if (!empty($response) && $response->code == 200) {
            /**
             * EUR wordt als base gebruikt dus die geeft de fixer.io niet terug
             */
            $currencies[] = "EUR";

            foreach ($response->body->rates as $key => $value) {
                $currencies[] = $key;
            }
        }

        return $currencies;
    }

    public function getAllLocalCurrencies()
    {
        return array(
            "EUR",
            "AUD",
            "BGN",
            "BRL",
            "CAD",
            "CHF",
            "CNY",
            "CZK",
            "DKK",
            "GBP",
            "HKD",
            "HRK",
            "HUF",
            "IDR",
            "ILS",
            "INR",
            "JPY",
            "KRW",
            "MXN",
            "MYR",
            "NOK",
            "NZD",
            "PHP",
            "PLN",
            "RON",
            "RUB",
            "SEK",
            "SGD",
            "THB",
            "TRY",
            "USD",
            "ZAR",
        );
    }
}