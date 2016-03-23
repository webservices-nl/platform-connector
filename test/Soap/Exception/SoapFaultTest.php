<?php

namespace WebservicesNl\Test\Soap\Client\Exception;

use League\FactoryMuffin\Facade as FactoryMuffin;
use WebservicesNl\Soap\Exception\SoapFault;

class SoapFaultTest extends \PHPUnit_Framework_TestCase
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
        FactoryMuffin::setCustomMaker(function ($class) {
            $faker = FactoryMuffin::getFaker();

            return new $class($faker->sentence(), $faker->text());
        });
        FactoryMuffin::loadFactories(dirname(dirname(__DIR__)) . '/Factories');
    }

    /**
     *
     */
    public function testSoapFaultInstance()
    {
        /** @var SoapFault $soapFault */
        $soapFault = FactoryMuffin::instance('WebservicesNl\Soap\Exception\SoapFault');

        self::assertInstanceOf('\SoapFault', $soapFault);
        self::assertInstanceOf('\WebservicesNl\Soap\Exception\SoapFault', $soapFault);
    }
}
