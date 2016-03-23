<?php

namespace WebservicesNl\Soap\Config;

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
    const FQCN = __NAMESPACE__ . '\%1$s\Config';

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
        if (!self::hasConfig($platform)) {
            throw new InputException("Could not find a platform config for '$platform'");
        }

        /** @var ConfigInterface $platformClassFQCN */
        $platformClassFQCN = sprintf(self::FQCN, ucfirst($platform));

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
        $platformClassFQCN = sprintf(self::FQCN, ucfirst($platform));

        return class_exists($platformClassFQCN);
    }
}
