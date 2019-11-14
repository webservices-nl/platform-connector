<?php

use WebservicesNl\Platform\Webservices\PlatformConfig;
use WebservicesNl\Protocol\Soap\Client\SoapConfig;
use WebservicesNl\Protocol\Soap\Config\Platform\Webservices\Converter;

League\FactoryMuffin\Facade::define(PlatformConfig::class, [
    'connectionTimeout' => 'numberBetween|10;120',
    'password' => 'word',
    'userName' => 'userName',
    'retryMinutes' => 'numberBetween|60;120',
    'responseTimeout' => 'numberBetween|20;30',
]);

League\FactoryMuffin\Facade::define(SoapConfig::class, [
    'converter' => function () {
        return new Converter();
    },
    'endPoints' => SoapConfig::getEndPoints(),
    'platformConfig' => function () {
        return League\FactoryMuffin\Facade::instance('WebservicesNl\Platform\Webservices\Config');
    },
    'soapHeaders' => [],
]);
