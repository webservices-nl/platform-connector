<?php

use WebservicesNl\Connector\Platform\Webservices\Config as PlatformConfig;

League\FactoryMuffin\Facade::define('WebservicesNl\Connector\Platform\Webservices\Config', [
    'endPoints'      => function () {
        return [$faker = League\FactoryMuffin\Facade::getFaker()->url];
    },
    'platformConfig' => function () {
        $password = League\FactoryMuffin\Facade::getFaker()->word;
        $username = League\FactoryMuffin\Facade::getFaker()->userName;

        return new PlatformConfig(['username' => $username, 'password' => $password]);
    },
    'soapHeaders'    => [],
    'converter'      => WebservicesNl\Soap\Config\Platform\Webservices\Converter::build(),
]);
