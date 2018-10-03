<?php

namespace WebservicesNl\Test\Platform\Webservices;

use League\FactoryMuffin\Facade as FactoryMuffin;
use WebservicesNl\Platform\Webservices\PlatformConfig;

class PlatformConfigTest extends \PHPUnit_Framework_TestCase
{
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
        FactoryMuffin::loadFactories(dirname(dirname(__DIR__)) . '/Factories');
    }

    /**
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws \InvalidArgumentException
     * @throws \WebservicesNl\Common\Exception\Server\NoServerAvailableException
     */
    public function testInstance()
    {
        /** @var \WebservicesNl\Platform\Webservices\PlatformConfig $platFormConfig */
        $platFormConfig = FactoryMuffin::create('WebservicesNl\Platform\Webservices\PlatformConfig');

        static::assertTrue(class_exists($platFormConfig->getClassName(true)));
        static::assertEquals($platFormConfig->getPlatformName(), PlatformConfig::PLATFORM_NAME);
        static::assertTrue(is_array($platFormConfig->toArray()));

        static::assertEquals($platFormConfig->getConnectorName(), 'WebservicesNl\Platform\Webservices\Connector');
    }

    /**
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     * @throws \InvalidArgumentException
     * @throws \WebservicesNl\Common\Exception\Server\NoServerAvailableException
     */
    public function testInstanceLoadFromArray()
    {
        /** @var \WebservicesNl\Platform\Webservices\PlatformConfig $platFormConfig */
        $platFormConfig = FactoryMuffin::create('WebservicesNl\Platform\Webservices\PlatformConfig');
        $platFormConfig->loadFromArray(['password' => 'secret', 'username' => 'johndoe']);

        static::assertEquals($platFormConfig->getUserName(), 'johndoe');
        static::assertEquals($platFormConfig->getPassword(), 'secret');
    }

    /**
     * @expectedException \WebservicesNl\Common\Exception\Client\InputException
     * @expectedExceptionMessage Not all mandatory config credentials are set
     *
     * @throws \WebservicesNl\Common\Exception\Client\InputException
     */
    public function testInstanceLoadFromArrayWithMissingValues()
    {
        /** @var \WebservicesNl\Platform\Webservices\PlatformConfig $platFormConfig */
        $platFormConfig = FactoryMuffin::create('WebservicesNl\Platform\Webservices\PlatformConfig');
        $platFormConfig->loadFromArray(['username' => 'johndoe', 'password' => null]);
    }
}
