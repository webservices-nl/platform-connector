<?php

namespace WebservicesNl\Protocol\Soap\Config;

use WebservicesNl\Common\Exception\Client\InputException;
use WebservicesNl\Platform\PlatformConfigInterface;
use WebservicesNl\Protocol\Soap\Client\SoapConfig;

/**
 * Class ConfigFactory.
 * Returns Config class for a given platform. Configured with platform and user credentials.
 */
class ConfigFactory
{
    const FQCN = 'WebservicesNl\\Protocol\\Soap\\Config\\Platform\\%1$s\\Config';

    /**
     * Return a Soap configuration for given platformConfig.
     *
     * @param PlatformConfigInterface $platform
     *
     * @return SoapConfig
     * @throws InputException
     */
    public static function config(PlatformConfigInterface $platform)
    {
        $platformName = $platform->getPlatformName();
        if (self::hasConfig($platformName) === false) {
            throw new InputException("Could not find a platform config for '$platformName'");
        }

        /** @var SoapConfig $soapConfig */
        $soapConfig = sprintf(self::FQCN, ucfirst($platformName));

        // return instance of config class with settings in place
        return $soapConfig::configure($platform);
    }

    /**
     * Check if config exists.
     *
     * @param string $className
     *
     * @return bool
     */
    public static function hasConfig($className)
    {
        $platformClassFQCN = sprintf(self::FQCN, ucfirst($className));

        return class_exists($platformClassFQCN);
    }
}
