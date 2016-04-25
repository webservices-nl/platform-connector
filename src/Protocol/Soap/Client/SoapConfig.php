<?php

namespace WebservicesNl\Protocol\Soap\Client;

use WebservicesNl\Common\Config\ConfigInterface;
use WebservicesNl\Platform\PlatformConfigInterface;
use WebservicesNl\Protocol\Soap\Exception\ConverterInterface;

/**
 * Class SoapConfig
 *
 * Soap config object that holds all data for a specific SOAP connection
 */
class SoapConfig implements ConfigInterface
{
    const DEFAULT_RESPONSE_TIMEOUT = 20;
    const RETRY_MINUTES = 60;

    /**
     * @var ConverterInterface
     */
    protected $converter;

    /**
     * List with Soap Server endpoints for platform.
     *
     * @var array
     */
    protected static $endPoints = [];

    /**
     * @var PlatformConfigInterface
     */
    protected $platformConfig;

    /**
     * @var array
     */
    protected $soapHeaders = [];

    /**
     * SoapConfig constructor.
     *
     * @param PlatformConfigInterface $config
     */
    public function __construct(PlatformConfigInterface $config)
    {
        $this->platformConfig = $config;
    }

    /**
     * Returns configured instance.
     *
     * @param PlatformConfigInterface $config
     *
     * @return ConfigInterface
     */
    public static function configure($config)
    {
        return new static($config);
    }

    /**
     * @return ConverterInterface
     */
    public function getConverter()
    {
        return $this->converter;
    }

    /**
     * Return array with Endpoints
     *
     * @return array
     */
    public static function getEndPoints()
    {
        return static::$endPoints;
    }

    /**
     * @return PlatformConfigInterface
     */
    public function getPlatformConfig()
    {
        return $this->platformConfig;
    }

    /**
     * @return array
     */
    public function getSoapHeaders()
    {
        return $this->soapHeaders;
    }

    /**
     * @return bool
     */
    public function hasConverter()
    {
        return $this->converter instanceof ConverterInterface;
    }

    /**
     * Return config as an array
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'converter' => $this->getConverter(),
            'endpoints'      => static::$endPoints,
            'platformConfig' => $this->platformConfig->toArray(),
            'retry_minutes'  => static::DEFAULT_RESPONSE_TIMEOUT,
            'soap_headers'   => (array)$this->getSoapHeaders(),
        ];
    }
}
