<?php

use League\FactoryMuffin\Facade as FactoryMuffin;
use WebservicesNl\Soap\Client\SoapSettings;

FactoryMuffin::define('WebservicesNl\Soap\Client\SoapSettings', [
    'authentication'    => function () {
        $values = [SOAP_AUTHENTICATION_BASIC, SOAP_AUTHENTICATION_DIGEST];

        return $values[array_rand($values)];
    },
    'cacheWsdl'         => 'numberBetween|0;3',
    'classMap'          => null,
    'compression'       => function () {

        $values = [SOAP_COMPRESSION_DEFLATE, SOAP_COMPRESSION_GZIP, SOAP_COMPRESSION_ACCEPT];

        return $values[array_rand($values)];
    },
    'connectionTimeout' => 'numberBetween|6;60',
    'context'           => '',
    'encoding'          => '',
    'exceptions'        => '',
    'features'          => function () {
        $values = [SOAP_SINGLE_ELEMENT_ARRAYS, SOAP_USE_XSI_ARRAY_TYPE, SOAP_WAIT_ONE_WAY_CALLS];

        return $values[array_rand($values)];
    },
    'keepAlive'         => 'boolean',
    'localCert'         => null,
    'passphrase'        => 'sentence',
    'password'          => 'fakePassword',
    'proxyHost'         => '',
    'proxyLogin'        => '',
    'proxyPassword'     => 'fakePassword',
    'proxyPort'         => 'numberBetween|1000;2000',
    'retryMinutes'      => 'numberBetween|10;120',
    'soapVersion'       => SOAP_1_1,
    'sslMethod'         => SOAP_SSL_METHOD_SSLv3,
    'responseTimeout'   => 'numberBetween|20;60',
    'sslMethod'         => function () {
        return array_rand(SoapSettings::$sslMethods);
    },
    'typeMap'           => null,
    'uri'               => '',
    'userAgent'         => 'bla',
    'username'          => 'userName',
]);
