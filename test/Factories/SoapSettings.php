<?php

use WebservicesNl\Protocol\Soap\Client\SoapSettings;

League\FactoryMuffin\Facade::define(SoapSettings::class, [
    'authentication' => function () {
        $values = [SOAP_AUTHENTICATION_BASIC, SOAP_AUTHENTICATION_DIGEST];

        return $values[array_rand($values)];
    },
    'cacheWsdl' => 'numberBetween|0;3',
    'classMap' => null,
    'compression' => function () {
        $values = [SOAP_COMPRESSION_DEFLATE, SOAP_COMPRESSION_GZIP, SOAP_COMPRESSION_ACCEPT];

        return $values[array_rand($values)];
    },
    'connectionTimeout' => 'numberBetween|6;60',
    'context' => '',
    'encoding' => 'UTF-8',
    'exceptions' => false,
    'features' => function () {
        $values = [SOAP_SINGLE_ELEMENT_ARRAYS, SOAP_USE_XSI_ARRAY_TYPE, SOAP_WAIT_ONE_WAY_CALLS];

        return $values[array_rand($values)];
    },
    'keepAlive' => 'boolean',
    'localCert' => null,
    'login' => 'userName',
    'passphrase' => 'sentence',
    'password' => 'word',
    'proxyHost' => '',
    'proxyLogin' => '',
    'proxyPassword' => 'word',
    'proxyPort' => 'numberBetween|1000;2000',
    'soapVersion' => function () {
        return SOAP_1_1;
    },
    'sslMethod' => function () {
        return array_rand(SoapSettings::$sslMethods);
    },
    'typeMap' => null,
    'uri' => '',
    'userAgent' => 'userAgent',
]);
