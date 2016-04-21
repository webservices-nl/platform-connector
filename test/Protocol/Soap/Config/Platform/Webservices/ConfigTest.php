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

    /**
     *
     */
    public function testConfigCreationWithWebservicesConfig()
    {
        /** @var \WebservicesNl\Platform\Webservices\PlatformConfig $platformConfig */
        $platformConfig = FactoryMuffin::instance('\WebservicesNl\Platform\Webservices\PlatformConfig');
        /** @var \WebservicesNl\Protocol\Soap\Config\Platform\Webservices\Config $config */
        $config = WebservicesConfig::configure($platformConfig);

        self::assertEquals($config->getUserName(), $platformConfig->getUserName());
        self::assertEquals($config->getPassword(), $platformConfig->getPassword());
        self::assertEquals($config->getRetryMinutes(), $platformConfig->getRetryMinutes());
        self::assertEquals($config->getResponseTimeout(), $platformConfig->getResponseTimeout());
        self::assertEquals($config->getConnectionTimeout(), $platformConfig->getConnectionTimeout());

        self::assertInstanceOf('\WebservicesNl\Protocol\Soap\Config\Platform\Webservices\Config', $config);
        self::assertInstanceOf(
            '\WebservicesNl\Protocol\Soap\Config\Platform\Webservices\Converter',
            $config->getConverter()
        );
    }

    /**
     *
     */
    public function testConfigCreationIsConvertedToArray()
    {
        /** @var \WebservicesNl\Platform\Webservices\PlatformConfig $platformConfig */
        $platformConfig = FactoryMuffin::instance('\WebservicesNl\Platform\Webservices\PlatformConfig');
        /** @var \WebservicesNl\Protocol\Soap\Config\Platform\Webservices\Config $soapConfig */
        $soapConfig = WebservicesConfig::configure($platformConfig);

        $resultArray = $soapConfig->toArray();

        self::assertArrayHasKey('username', $resultArray);
        self::assertCount(8, $resultArray);
    }

    /**
     *
     */
    public function testConfigArrayIsBorked()
    {
        /** @var \WebservicesNl\Platform\Webservices\PlatformConfig $platformConfig */
        $platformConfig = FactoryMuffin::instance(
            '\WebservicesNl\Platform\Webservices\PlatformConfig',
            ['userName' => function () {
                return null;
            },]
        );

        /** @var \WebservicesNl\Protocol\Soap\Config\Platform\Webservices\Config $soapConfig */
        $soapConfig = WebservicesConfig::configure($platformConfig);

        $resultArray = $soapConfig->toArray();

        self::assertArrayNotHasKey('username', $resultArray);
        self::assertNull($soapConfig->getUserName());
        self::assertCount(7, $resultArray);
    }
}
