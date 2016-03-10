<?php

namespace Webservicesnl\Test\Soap\Client\Config;

use Webservicesnl\Soap\Client\Config\WebservicesConfig;

/**
 * Class WebservicesConfigTest.
 *
 */
class WebservicesConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Webservicesnl\Common\Exception\Client\InputException
     * @expectedExceptionMessage Settings is not a SoapSettings object or array
     *
     * @throws \Webservicesnl\Common\Exception\Client\InputException
     */
    public function testConfigCreationWithInvalidArgument()
    {
        WebservicesConfig::configure(null);
    }

    /**
     * @expectedException \Webservicesnl\Common\Exception\Client\InputException
     * @expectedExceptionMessage Not all mandatory config credentials are set
     *
     * @throws \Webservicesnl\Common\Exception\Client\InputException
     */
    public function testConfigCreationWithEmptyArray()
    {
        WebservicesConfig::configure([]);
    }
}
