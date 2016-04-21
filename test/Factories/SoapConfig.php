<?php

League\FactoryMuffin\Facade::define('WebservicesNl\Platform\Webservices\PlatformConfig', [
    'connectionTimeout' => 'numberBetween|10;120',
    'password'          => 'word',
    'userName'          => 'userName',
    'retryMinutes'      => 'numberBetween|60;120',
    'responseTimeout'   => 'numberBetween|20;30',
]);

League\FactoryMuffin\Facade::define('WebservicesNl\Protocol\Soap\Client\SoapConfig', [
    'converter'      => function () {
        return new \WebservicesNl\Protocol\Soap\Config\Platform\Webservices\Converter();
    },
    'endPoints'      => WebservicesNl\Protocol\Soap\Client\SoapConfig::getEndPoints(),
    'platformConfig' => function () {
        return League\FactoryMuffin\Facade::instance('WebservicesNl\Platform\Webservices\Config');
    },
    'soapHeaders'    => [],
]);
