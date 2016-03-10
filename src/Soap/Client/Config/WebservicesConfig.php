<?php

namespace Webservicesnl\Soap\Client\Config;

use Webservicesnl\Common\Config\ConfigInterface;
use Webservicesnl\Common\Exception\Client\InputException;
use Webservicesnl\Soap\Client\SoapSettings;

/**
 * Class WebservicesConfig.
 *
 */
class WebservicesConfig implements ConfigInterface
{
    const PLATFORM_NAME = 'webservices';
    const SOAPHEADER_URL = 'http://www.webservices.nl/soap/';

    /**
     * List with server endpoints.
     *
     * @var array
     */
    private static $endPoints = [
        'https://ws1.webservices.nl/soap_doclit?wsdl',
        'https://ws2.webservices.nl/soap_doclit?wsdl',
    ];

    /**
     * @param SoapSettings|array $settings
     *
     * @return array
     *
     * @throws InputException
     */
    public static function configure($settings)
    {
        if (is_array($settings)) {
            $settings = SoapSettings::loadFromArray($settings);
        } elseif (!$settings instanceof SoapSettings) {
            throw new InputException('Settings is not a SoapSettings object or array');
        }

        return [
            'soapHeaders' => [
                new \SoapHeader(
                    self::SOAPHEADER_URL,
                    'headerLogin',
                    [
                        'username' => $settings->getUsername(),
                        'password' => $settings->getPassword(),
                    ],
                    true
                ),
            ],
            'endPoints' => self::$endPoints,
        ];
    }
}
