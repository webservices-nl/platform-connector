<?php

namespace WebservicesNl\Test;

use InvalidArgumentException;
use League\FactoryMuffin\Facade as FactoryMuffin;
use WebservicesNl\Platform\Webservices\PlatformConfig;

/**
 * Class AbstractConfigTest
 */
class AbstractConfigTest extends \PHPUnit_Framework_TestCase
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
        FactoryMuffin::loadFactories(dirname(__DIR__) . '/Factories');
    }


    /**
     * @throws InvalidArgumentException
     */
    public function testInstance()
    {
        /** @var PlatformConfig $platFormConfig */
        $platFormConfig = FactoryMuffin::create(PlatformConfig::class);

        static::assertTrue(class_exists($platFormConfig->getClassName(true)));
        static::assertEquals($platFormConfig->getPlatformName(), $platFormConfig::PLATFORM_NAME);
        static::assertInternalType('array', $platFormConfig->toArray());
    }
}
