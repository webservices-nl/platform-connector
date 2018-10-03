<?php

namespace WebservicesNl\Test;

use League\FactoryMuffin\Facade as FactoryMuffin;

/**
 * Class AbstractConfigTest
 */
class AbstractConfigTest extends \PHPUnit_Framework_TestCase
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
        FactoryMuffin::loadFactories(dirname(__DIR__) . '/Factories');
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
        static::assertEquals($platFormConfig->getPlatformName(), $platFormConfig::PLATFORM_NAME);
        static::assertTrue(is_array($platFormConfig->toArray()));
    }
}
