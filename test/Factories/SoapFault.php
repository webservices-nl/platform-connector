<?php

use League\FactoryMuffin\Facade as FactoryMuffin;

FactoryMuffin::define('WebservicesNl\Soap\Exception\SoapFault', [
    'detail'       => 'sentence',
    'faultActor'   => 'something',
    'faultCode'    => 'Server',
    'faultName'    => 'word',
    'faultString'  => 'Something went wrong',
    'headerFault'  => '',
    'errorMessage' => 'sentence',
]);
