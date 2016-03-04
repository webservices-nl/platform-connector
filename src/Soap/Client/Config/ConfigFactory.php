<?php

namespace Webservicesnl\Soap\Client\Config;

use Webservicesnl\Exception\Client\Input\InvalidException;
use Webservicesnl\Exception\Client\InputException;
use Webservicesnl\Common\Config\ConfigInterface;
use Webservicesnl\Soap\Client\SoapSettings;


/**
 * Class ConfigFactory
 *
 * @package Webservicesnl\Soap\Client\Config
 */
class ConfigFactory
{
    /**
     * Return platform configuration for given platform
     *
     * @param string             $platform
     * @param SoapSettings|array $settings
     *
     * @return mixed
     * @throws InputException
     */
    public static function config($platform, $settings)
    {
        if (!is_string($platform) || empty($platform)) {
            throw new InvalidException("That just won't jive");
        }

        /** @var ConfigInterface $platformClassFQCN */
        $platformClassFQCN = __NAMESPACE__ . '\\' . ucfirst($platform) . 'Config';
        if (!class_exists($platformClassFQCN)) {
            throw new InputException("Could not find a config for '$platform'");
        }

        // return config file with settings vars in place
        return $platformClassFQCN::configure($settings);
    }
}
