<?php

namespace WebservicesNl\Test\Protocol\Soap\Client;

use League\FactoryMuffin\Facade as FactoryMuffin;
use WebservicesNl\Common\Exception\Client\InputException;
use WebservicesNl\Protocol\Soap\Client\SoapSettings;

/**
 * Class SoapSettingsTest.
 */
class SoapSettingsTest extends \PHPUnit_Framework_TestCase
{
    public static function setupBeforeClass()
    {
        FactoryMuffin::setFakerLocale('nl_NL');
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

    /**
     * @throws InputException
     */
    public function testSettingsConnectionTimeout()
    {
        $this->expectException(InputException::class);
        $this->expectExceptionMessage('Not a valid timeout');

        $settings = new SoapSettings();
        $settings->setConnectionTimeout('bla');
    }

    /**
     * Checking getter and setter (might be a silly test).
     */
    public function testMappingToArray()
    {
        /** @var SoapSettings $settings */
        $settings = FactoryMuffin::create(SoapSettings::class);
        $array = $settings->toArray();

        // accessing private properties ...
        array_walk($array, function ($value, $key) use ($settings) {
            $name = 'get' . ucfirst($key);
            if (!method_exists($settings, $name)) {
                $name = 'has' . ucfirst($key);
            }
            if (!method_exists($settings, $name)) {
                $name = 'is' . ucfirst($key);
            }

            static::assertEquals($settings->{$name}(), $value, "SoapSetting should have $key value");
        });
    }
}
