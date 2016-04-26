<?php

namespace WebservicesNl\Test\Protocol\Soap\Client\Exception;

use League\FactoryMuffin\Facade as FactoryMuffin;
use WebservicesNl\Protocol\Soap\Exception\SoapFault;

/**
 * Class SoapFaultTest
 *
 * @package WebservicesNl\Test\Protocol\Soap\Client\Exception
 */
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
        FactoryMuffin::loadFactories(dirname(dirname(dirname(__DIR__))) . '/Factories');
    }

    /**
     *
     */
    public function testSoapFaultInstance()
    {
        /** @var SoapFault $soapFault */
        $soapFault = FactoryMuffin::instance('WebservicesNl\Protocol\Soap\Exception\SoapFault');

        static::assertInstanceOf('\SoapFault', $soapFault);
        static::assertInstanceOf('\WebservicesNl\Protocol\Soap\Exception\SoapFault', $soapFault);
    }
}
