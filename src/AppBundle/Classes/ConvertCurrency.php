<?php

namespace AppBundle\Classes;

class ConvertCurrency
{
    public $url = '';
    public $convertFromParam = '';
    public $convertToParam = '';
    public $responseType = '';

    public function __construct($url, $convertFrom, $convertTo, $responseType)
    {
        $this->url = $url;
        $this->convertFromParam = $convertFrom;
        $this->convertToParam = $convertTo;
        $this->responseType = $responseType;
    }
}