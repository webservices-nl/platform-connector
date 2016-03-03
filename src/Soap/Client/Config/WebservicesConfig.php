<?php

namespace Webservicesnl\Soap\Client\Config;

use Webservicesnl\Common\Config\ConfigInterface;

class WebservicesConfig implements ConfigInterface
{
    const PLATFORM_NAME = 'webservices';
    const SOAPHEADER_URL = 'http://www.webservices.nl/soap/';

    /**
     * List with server endpoints
     *
     * @var array
     */
    private static $endPoints = [
        'https://ws1.webservices.nl/soap_doclit?wsdl',
        'https://ws2.webservices.nl/soap_doclit?wsdl',
    ];

    /**
     * @param array $settings
     *
     * @return array
     */
    public static function configure(array $settings)
    {
        return [
            'soapHeaders' => [
                new \SoapHeader(
                    self::SOAPHEADER_URL,
                    'headerLogin',
                    [
                        'username' => $settings['username'],
                        'password' => $settings['password'],
                    ],
                    true
                ),
            ],
            'endPoints'   => self::$endPoints,
        ];
    }
}