<?php

namespace Webservicesnl\Soap\Client\Config;

use Webservicesnl\Exception\Client\InputException;
use Webservicesnl\Common\Config\ConfigInterface;


/**
 * Class ConfigFactory
 *
 * @package Webservicesnl\Soap\Client\Config
 */
class ConfigFactory
{
    /**
     * @param string $platform
     * @param array  $settings
     *
     * @return mixed
     * @throws InputException
     */
    public static function config($platform, array $settings)
    {
        /** @var ConfigInterface $classFQCN */
        $classFQCN = __NAMESPACE__ . '\\' . ucfirst($platform) . 'Config';
        if (!class_exists($classFQCN)) {
            throw new InputException("Could not find a config for '$platform' ");
        }

        return $classFQCN::configure($settings);
    }
}
