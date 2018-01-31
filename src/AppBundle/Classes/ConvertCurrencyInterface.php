<?php

namespace AppBundle\Classes;

interface ConvertCurrencyInterface
{
    public function Convert($currencyFrom, $currencyTo);
}