<?php

namespace Webservicesnl\Soap\Client\Config;

use Webservicesnl\Common\Config\ConfigInterface;
use Webservicesnl\Exception\Client\InputException;

/**
 * Class ConfigFactory
 *
 */
class ConfigFactory
{
    /**
     * @param string $platform
     * @param array  $settings
     *
     * @return mixed
     *
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
