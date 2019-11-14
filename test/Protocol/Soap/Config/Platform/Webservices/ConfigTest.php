<?php

namespace WebservicesNl\Test\Protocol\Soap\Config\Platform\Webservices;

use League\FactoryMuffin\Facade as FactoryMuffin;
use WebservicesNl\Platform\Webservices\PlatformConfig;
use WebservicesNl\Protocol\Soap\Config\Platform\Webservices\Config as WebservicesConfig;
use WebservicesNl\Protocol\Soap\Config\Platform\Webservices\Converter;

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
        FactoryMuffin::setCustomSaver(
/** @noinspection StaticClosureCanBeUsedInspection */ function () {
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
        /** @var PlatformConfig $platformConfig */
        $platformConfig = FactoryMuffin::instance(PlatformConfig::class);
        /** @var WebservicesConfig $config */
        $config = WebservicesConfig::configure($platformConfig);

        static::assertEquals($config->getUserName(), $platformConfig->getUserName());
        static::assertEquals($config->getPassword(), $platformConfig->getPassword());
        static::assertEquals($config->getRetryMinutes(), $platformConfig->getRetryMinutes());
        static::assertEquals($config->getResponseTimeout(), $platformConfig->getResponseTimeout());
        static::assertEquals($config->getConnectionTimeout(), $platformConfig->getConnectionTimeout());

        static::assertInstanceOf(WebservicesConfig::class, $config);
        static::assertInstanceOf(
            Converter::class,
            $config->getConverter()
        );
    }

    public function testConfigCreationIsConvertedToArray()
    {
        /** @var PlatformConfig $platformConfig */
        $platformConfig = FactoryMuffin::instance(PlatformConfig::class);
        /** @var WebservicesConfig $soapConfig */
        $soapConfig = WebservicesConfig::configure($platformConfig);

        $resultArray = $soapConfig->toArray();

        static::assertArrayHasKey('username', $resultArray);
        static::assertCount(8, $resultArray);
    }

    public function testConfigArrayIsBorked()
    {
        /** @var PlatformConfig $platformConfig */
        $platformConfig = FactoryMuffin::instance(
            PlatformConfig::class,
            ['userName' => function () {
                return null;
            }, ]
        );

        /** @var WebservicesConfig $soapConfig */
        $soapConfig = WebservicesConfig::configure($platformConfig);

        $resultArray = $soapConfig->toArray();

        static::assertArrayNotHasKey('username', $resultArray);
        static::assertNull($soapConfig->getUserName());
        static::assertCount(7, $resultArray);
    }
}
