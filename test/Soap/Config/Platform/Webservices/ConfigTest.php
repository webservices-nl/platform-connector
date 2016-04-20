<?php

namespace WebservicesNl\Test\Soap\Config\Platform\Webservices;

use League\FactoryMuffin\Facade as FactoryMuffin;
use WebservicesNl\Soap\Config\Platform\Webservices\Config as WebservicesConfig;

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
        FactoryMuffin::loadFactories(dirname(dirname(dirname(dirname(__DIR__)))) . '/Factories');
    }

    /**
     *
     */
    public function testConfigCreationWithWebservicesConfig()
    {
        /** @var \WebservicesNl\Connector\Platform\Webservices\Config $platformConfig */
        $platformConfig = FactoryMuffin::instance('\WebservicesNl\Connector\Platform\Webservices\Config');
        /** @var \WebservicesNl\Soap\Config\Platform\Webservices\Config $soapConfig */
        $soapConfig = WebservicesConfig::configure($platformConfig);

        self::assertEquals($soapConfig->getUserName(), $platformConfig->getUserName());
        self::assertEquals($soapConfig->getPassword(), $platformConfig->getPassword());
        self::assertEquals($soapConfig->getRetryMinutes(), $platformConfig->getRetryMinutes());
        self::assertEquals($soapConfig->getResponseTimeout(), $platformConfig->getResponseTimeout());
        self::assertEquals($soapConfig->getConnectionTimeout(), $platformConfig->getConnectionTimeout());

        self::assertInstanceOf('\WebservicesNl\Soap\Config\Platform\Webservices\Config', $soapConfig);
        self::assertInstanceOf(
            '\WebservicesNl\Soap\Config\Platform\Webservices\Converter',
            $soapConfig->getConverter()
        );
    }

    /**
     *
     */
    public function testConfigCreationIsConvertedToArray()
    {
        /** @var \WebservicesNl\Connector\Platform\Webservices\Config $platformConfig */
        $platformConfig = FactoryMuffin::instance('\WebservicesNl\Connector\Platform\Webservices\Config');
        /** @var \WebservicesNl\Soap\Config\Platform\Webservices\Config $soapConfig */
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
        /** @var \WebservicesNl\Connector\Platform\Webservices\Config $platformConfig */
        $platformConfig = FactoryMuffin::instance(
            '\WebservicesNl\Connector\Platform\Webservices\Config',
            [
                'userName' => function () {
                    return null;
                },
            ]
        );

        /** @var \WebservicesNl\Soap\Config\Platform\Webservices\Config $soapConfig */
        $soapConfig = WebservicesConfig::configure($platformConfig);

        $resultArray = $soapConfig->toArray();

        self::assertArrayNotHasKey('username', $resultArray);
        self::assertNull($soapConfig->getUserName());
        self::assertCount(7, $resultArray);
    }
}
