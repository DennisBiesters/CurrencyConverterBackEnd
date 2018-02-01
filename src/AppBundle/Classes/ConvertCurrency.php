<?php

namespace AppBundle\Classes;

class ConvertCurrency
{
    public $url = '';
    public $convertFromParam = '';
    public $convertToParam = '';
    public $responseType = '';
    public $available = '';

    public function __construct($url, $convertFrom, $convertTo, $responseType, $available)
    {
        $this->url = $url;
        $this->convertFromParam = $convertFrom;
        $this->convertToParam = $convertTo;
        $this->responseType = $responseType;
        $this->available = $available;
    }
}