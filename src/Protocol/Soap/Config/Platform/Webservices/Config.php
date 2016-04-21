<?php

namespace WebservicesNl\Protocol\Soap\Config\Platform\Webservices;

use WebservicesNl\Platform\Webservices\PlatformConfig as PlatformConfig;
use WebservicesNl\Protocol\Soap\Client\SoapConfig;

/**
 * Class WebservicesConfig.
 *
 * Container for all settings to connect to Webservices platform. User credentials and time out settings. etc
 */
class Config extends SoapConfig
{
    const DEFAULT_RESPONSE_TIMEOUT = 20;
    const RETRY_MINUTES = 60;
    const SOAPHEADER_URL = 'http://www.webservices.nl/soap/';

    /**
     * List with Soap Server endpoints.
     *
     * @var array
     */
    protected static $endPoints = [
        'https://ws1.webservices.nl/soap',
        'https://ws2.webservices.nl/soap',
    ];

    /**
     * Config constructor.
     *
     * @param platformConfig $config
     */
    public function __construct(platformConfig $config)
    {
        $this->converter = Converter::build();
        $this->soapHeaders[] = new \SoapHeader(
            self::SOAPHEADER_URL,
            'HeaderLogin',
            [
                'username' => $config->getUserName(),
                'password' => $config->getPassword(),
            ],
            true
        );

        parent::__construct($config);
    }

    /**
     * @return Converter
     */
    public function getConverter()
    {
        return $this->converter;
    }


    /**
     * @return int
     */
    public function getConnectionTimeout()
    {
        return $this->getPlatformConfig()->getConnectionTimeout();
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->getPlatformConfig()->getPassword();
    }

    /**
     * @return int
     */
    public function getRetryMinutes()
    {
        return $this->getPlatformConfig()->getRetryMinutes();
    }

    /**
     * @return int
     */
    public function getResponseTimeout()
    {
        return $this->getPlatformConfig()->getResponseTimeout();
    }

    /**
     * @return array
     */
    public function getSoapHeaders()
    {
        return $this->soapHeaders;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->getPlatformConfig()->getUserName();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_filter([
            'converter'          => $this->getConverter(),
            'connection_timeout' => $this->getPlatformConfig()->getConnectionTimeout(),
            'endpoints'          => self::$endPoints,
            'password'           => $this->getPlatformConfig()->getPassword(),
            'retry_minutes'      => $this->getPlatformConfig()->getRetryMinutes(),
            'soap_headers'       => (array)$this->getSoapHeaders(),
            'timeout'            => $this->getPlatformConfig()->getResponseTimeout(),
            'username'           => $this->getPlatformConfig()->getUserName(),
        ]);
    }

    /**
     * @return PlatformConfig
     */
    public function getPlatformConfig()
    {
        return $this->platformConfig;
    }
}
