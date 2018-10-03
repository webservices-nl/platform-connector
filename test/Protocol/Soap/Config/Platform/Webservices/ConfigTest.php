<?php

namespace WebservicesNl\Test\Protocol\Soap\Config\Platform\Webservices;

use League\FactoryMuffin\Facade as FactoryMuffin;
use WebservicesNl\Protocol\Soap\Config\Platform\Webservices\Config as WebservicesConfig;

/**
 * Class WebservicesConfigTest.
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Setup.
     */
    public static function setupBeforeClass()
    {
        FactoryMuffin::setCustomSaver(function () {
            return true;
        });

        FactoryMuffin::setCustomSetter(function ($object, $name, $value) {
            $name = 'set' . ucfirst(strtolower($name));
            if (method_exists($object, $name)) {
                $object->{$name}($value);
            }
        });
        FactoryMuffin::loadFactories(dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/Factories');
    }

    public function testConfigCreationWithWebservicesConfig()
    {
        /** @var \WebservicesNl\Platform\Webservices\PlatformConfig $platformConfig */
        $platformConfig = FactoryMuffin::instance('\WebservicesNl\Platform\Webservices\PlatformConfig');
        /** @var \WebservicesNl\Protocol\Soap\Config\Platform\Webservices\Config $config */
        $config = WebservicesConfig::configure($platformConfig);

        static::assertEquals($config->getUserName(), $platformConfig->getUserName());
        static::assertEquals($config->getPassword(), $platformConfig->getPassword());
        static::assertEquals($config->getRetryMinutes(), $platformConfig->getRetryMinutes());
        static::assertEquals($config->getResponseTimeout(), $platformConfig->getResponseTimeout());
        static::assertEquals($config->getConnectionTimeout(), $platformConfig->getConnectionTimeout());

        static::assertInstanceOf('\WebservicesNl\Protocol\Soap\Config\Platform\Webservices\Config', $config);
        static::assertInstanceOf(
            '\WebservicesNl\Protocol\Soap\Config\Platform\Webservices\Converter',
            $config->getConverter()
        );
    }

    public function testConfigCreationIsConvertedToArray()
    {
        /** @var \WebservicesNl\Platform\Webservices\PlatformConfig $platformConfig */
        $platformConfig = FactoryMuffin::instance('\WebservicesNl\Platform\Webservices\PlatformConfig');
        /** @var \WebservicesNl\Protocol\Soap\Config\Platform\Webservices\Config $soapConfig */
        $soapConfig = WebservicesConfig::configure($platformConfig);

        $resultArray = $soapConfig->toArray();

        static::assertArrayHasKey('username', $resultArray);
        static::assertCount(8, $resultArray);
    }

    public function testConfigArrayIsBorked()
    {
        /** @var \WebservicesNl\Platform\Webservices\PlatformConfig $platformConfig */
        $platformConfig = FactoryMuffin::instance(
            '\WebservicesNl\Platform\Webservices\PlatformConfig',
            ['userName' => function () {
                return null;
            }, ]
        );

        /** @var \WebservicesNl\Protocol\Soap\Config\Platform\Webservices\Config $soapConfig */
        $soapConfig = WebservicesConfig::configure($platformConfig);

        $resultArray = $soapConfig->toArray();

        static::assertArrayNotHasKey('username', $resultArray);
        static::assertNull($soapConfig->getUserName());
        static::assertCount(7, $resultArray);
    }
}
