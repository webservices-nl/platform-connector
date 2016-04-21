<?php

namespace WebservicesNl\Test\Protocol\Soap\Client;

use League\FactoryMuffin\Facade as FactoryMuffin;
use WebservicesNl\Platform\Webservices\PlatformConfig;
use WebservicesNl\Protocol\Soap\Client\SoapConfig;

/**
 * Class SoapConfigTest
 *
 * @package WebservicesNl\Test\Protocol\Soap\Client
 */
class SoapConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
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

        FactoryMuffin::loadFactories(dirname(dirname(dirname(__DIR__))) . '/Factories');
    }

    public function testEmptyInstance()
    {
        /** @var PlatformConfig $platFormConfig */
        $platFormConfig = FactoryMuffin::create('WebservicesNl\Platform\Webservices\PlatformConfig');
        $soapConfig = new SoapConfig($platFormConfig);
        
        self::assertFalse($soapConfig->hasConverter());
        self::assertEmpty($soapConfig->getSoapHeaders());
        self::assertEquals($platFormConfig, $soapConfig->getPlatformConfig());
        self::assertAttributeInstanceOf('WebservicesNl\Platform\Webservices\PlatformConfig', 'platformConfig', $soapConfig);
        self::assertEquals($soapConfig::getEndPoints(), SoapConfig::getEndPoints());
        self::assertCount(5, $soapConfig->toArray());

        $static = SoapConfig::configure($platFormConfig);

        self::assertEquals($static, $soapConfig);
    }
}
