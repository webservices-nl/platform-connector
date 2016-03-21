<?php

namespace WebservicesNl\Soap\Client\Config;

use WebservicesNl\Common\Config\ConfigInterface;
use WebservicesNl\Common\Exception\Client\InputException;
use WebservicesNl\Soap\Client\SoapSettings;

/**
 * Class ConfigFactory.
 *
 * Returns Config class for a given platform
 */
class ConfigFactory
{
    /**
     * Return platform configuration for given platform.
     *
     * @param string             $platform
     * @param SoapSettings|array $settings
     *
     * @return mixed
     *
     * @throws InputException
     */
    public static function config($platform, $settings)
    {
        /** @var ConfigInterface $platformClassFQCN */
        $platformClassFQCN = __NAMESPACE__ . '\\' . ucfirst($platform) . 'Config';
        if (!class_exists($platformClassFQCN)) {
            throw new InputException("Could not find a platform config for '$platform'");
        }

        // return config file with settings vars in place
        return $platformClassFQCN::configure($settings);
    }

    /**
     * Check if config exists.
     *
     * @param string $platform
     *
     * @return bool
     */
    public static function hasConfig($platform)
    {
        /** @var ConfigInterface $platformClassFQCN */
        $platformClassFQCN = __NAMESPACE__ . '\\' . ucfirst((string)$platform) . 'Config';

        return class_exists($platformClassFQCN);
    }
}
