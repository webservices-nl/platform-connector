<?php

namespace WebservicesNl\Test\Platform\Webservices;

use League\FactoryMuffin\Facade as FactoryMuffin;
use WebservicesNl\Common\Exception\Client\InputException;
use WebservicesNl\Platform\Webservices\Connector;
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
     * @throws \InvalidArgumentException
     */
    public function testInstance()
    {
        /** @var PlatformConfig $platFormConfig */
        $platFormConfig = FactoryMuffin::create(PlatformConfig::class);

        static::assertTrue(class_exists($platFormConfig->getClassName(true)));
        static::assertEquals($platFormConfig->getPlatformName(), PlatformConfig::PLATFORM_NAME);
        static::assertInternalType('array', $platFormConfig->toArray());

        static::assertEquals($platFormConfig->getConnectorName(), Connector::class);
    }

    /**
     * @throws InputException
     * @throws \InvalidArgumentException
     */
    public function testInstanceLoadFromArray()
    {
        /** @var PlatformConfig $platFormConfig */
        $platFormConfig = FactoryMuffin::create(PlatformConfig::class);
        $platFormConfig->loadFromArray(['password' => 'secret', 'username' => 'johndoe']);

        static::assertEquals($platFormConfig->getUserName(), 'johndoe');
        static::assertEquals($platFormConfig->getPassword(), 'secret');
    }

    /**
     * @throws InputException
     */
    public function testInstanceLoadFromArrayWithMissingValues()
    {
        $this->expectException(InputException::class);
        $this->expectExceptionMessage('Not all mandatory config credentials are set');

        /** @var PlatformConfig $platFormConfig */
        $platFormConfig = FactoryMuffin::create(PlatformConfig::class);
        $platFormConfig->loadFromArray(['username' => 'johndoe', 'password' => null]);
    }
}
