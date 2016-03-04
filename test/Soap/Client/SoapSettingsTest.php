<?php

namespace Webservicesnl\Test\Soap\Client;

use League\FactoryMuffin\Facade as FactoryMuffin;
use Webservicesnl\Soap\Client\SoapSettings;

class SoapSettingsTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     */
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
        FactoryMuffin::loadFactories(dirname(dirname(__DIR__)) . '/Factories');
    }

    /**
     * @expectedException \Webservicesnl\Exception\Client\Input\InvalidException
     * @expectedExceptionMessage Not all mandatory config credentials are set
     */
    public function testInstantiationWithoutCredentials()
    {
        SoapSettings::loadFromArray([]);
    }

    /**
     * @expectedException \Webservicesnl\Exception\Client\Input\InvalidException
     * @expectedExceptionMessage Not a valid timeout
     */
    public function testSettingsConnectionTimeout()
    {
        $settings = new SoapSettings();
        $settings->setConnectionTimeout('bla');
    }

    /**
     *
     */
    public function testInstantiationWithCredentials()
    {
        $options = ['username' => 'john', 'password' => 'secret'];
        $settings = SoapSettings::loadFromArray($options);

        $this->assertNotEmpty($settings->getPassword(), 'SoapSettings should have a password');
        $this->assertNotEmpty($settings->getUsername(), 'SoapSettings should have a username');
    }

    /**
     * Checking getter and setter (might be a silly test).
     */
    public function testMappingToArray()
    {
        /** @var \Webservicesnl\Soap\Client\SoapSettings $settings */
        $settings = FactoryMuffin::create('Webservicesnl\Soap\Client\SoapSettings');
        $array = $settings->toArray();

        // accessing private properties ...
        array_walk($array, function ($value, $key) use ($settings, $array) {
            $name = 'get' . ucfirst($key);
            if (!method_exists($settings, $name)) {
                $name = 'has' . ucfirst($key);
            }
            if (!method_exists($settings, $name)) {
                $name = 'is' . ucfirst($key);
            }

            $this->assertEquals($settings->{$name}(), $value, "SoapSetting should have $key value");
        });
    }
}
